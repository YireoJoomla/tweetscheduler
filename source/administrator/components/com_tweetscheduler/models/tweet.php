<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright Yireo.com 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Tweetscheduler Tweet model
 */
class TweetschedulerModelTweet extends YireoModel
{
	/**
	 * Override the orderby_title
	 *
	 * @var string
	 */
	protected $_orderby_title = 'message';

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct('tweet');
	}

	/**
	 * Method to store the model
	 *
	 * @param mixed $data
	 *
	 * @return bool
	 */
	public function store($data)
	{
		// Flatten the account_id
		if (isset($data['account_id']) && is_array($data['account_id']))
		{
			$data['account_id'] = implode(',', $data['account_id']);
		}

		// Shorten the URLs in the message
		if ($this->params->get('autoshorten', 1) == 1 && !empty($data['message']))
		{
			require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/shortener.php';
			$data['message'] = TweetschedulerHelperShortener::autoshortenText($data['message']);
		}

		// Convert date to proper timezone
		$timezone          = TweetschedulerHelper::getTimezone();
		$post_date         = new JDate($data['post_date'], $timezone);
		$data['post_date'] = $post_date->format('Y-m-d H:i:s', false, false);

		$session = JFactory::getSession();
		$session->set('tweetscheduler.post_date', $post_date);

		// Set UTC flag
		$data['utc'] = 1;

		return parent::store($data);
	}

	/**
	 * Override buildQuery method
	 *
	 * @return string
	 */
	protected function buildQuery($query = '')
	{
		$query = "SELECT {tableAlias}.*, category.title AS category_name, category.url AS category_url, ";
		$query .= " editor.name AS editor FROM {table} AS {tableAlias} ";
		$query .= " LEFT JOIN #__tweetscheduler_categories AS category ON {tableAlias}.category_id = category.id ";
		$query .= " LEFT JOIN #__users AS editor ON {tableAlias}.checked_out = editor.id ";

		return parent::buildQuery($query);
	}

	/**
	 * Method to modify the data once it is loaded
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function onDataLoad($data)
	{
		if (is_string($data->account_id))
		{
			$data->account_id = explode(',', trim($data->account_id));
		}

		if (!empty($data->account_id))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__tweetscheduler_accounts'));
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $data->account_id) . ')');
			$db->setQuery($query);
			$data->accounts = $db->loadObjectList();
		}

		if (isset($data->utc) && $data->utc == 1)
		{
			$timezone  = TweetschedulerHelper::getTimezone();
			$post_date = new JDate($data->post_date);
			$post_date->setTimezone($timezone);
			$data->post_date = $post_date->format('Y-m-d H:i', $timezone);
		}
		else
		{
			$data->utc = 0;
		}

		return $data;
	}

	/**
	 * Method to post a twitter-message
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function postTwitter($data)
	{
		// Get the object
		$twitter = TweetschedulerHelper::getTwitter($data);

		// Post the data
		try
		{
			$twitterInfo = $twitter->post_statusesUpdate(array('status' => $data->message));

			return $twitterInfo->response;
		}
		catch (Exception $e)
		{
			return array('error' => $e->getMessage());
		}
	}

	/**
	 * Method to post a facebook-message
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function postFacebook($data)
	{
		// Get the object
		$facebook = TweetschedulerHelper::getFacebook($data);
		$user     = $facebook->getUser();

		if (!$user > 0)
		{
			return array('error' => 'Unable to login into Facebook');
		}

		// Post the data
		$post = array('message' => $data->message, 'name' => $data->title, 'caption' => $data->title,);

		if (!empty($data->category_name) && !empty($data->category_url))
		{
			$post['actions'] = array(array('name' => $data->category_name, 'link' => $data->category_url));
		}

		if (!empty($data->category_url))
		{
			$post['link'] = $data->category_url;
		}

		// @todo: Create a specific field for this / use image from article
		if (!empty($data->image))
		{
			$post['image'] = $data->image;
		}

		// @todo: Replace /feed/ with /milestones/ (title, description, start_time)
		$apiUrl = '/me/feed';

		if (!empty($data->page))
		{
			$pageToken = null;
			$accounts  = $facebook->api('/me/accounts');

			if (!empty($accounts['data']))
			{
				foreach ($accounts['data'] as $account)
				{
					if ($account['id'] == $data->page)
					{
						$pageToken = $account['access_token'];
					}
				}
			}

			if (!empty($pageToken))
			{
				unset($post['actions']);
				$apiUrl = '/' . $data->page . '/feed?access_token=' . $pageToken;
			}
		}

		$result = $facebook->api($apiUrl, 'post', $post);

		return $result;
	}

	/**
	 * Method to post a Linkedin-message
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function postLinkedin($data)
	{
		// Get the object
		$linkedin = TweetschedulerHelper::getLinkedin($data);

		// Post the data
		$content = array(
			'title'         => $data->title,
			'description'   => $data->message,
			'submitted-url' => $data->category_url
		);

		try
		{
			$response = $linkedin->share('new', $content, false);
		}
		catch (Exception $e)
		{
			$response = array('error' => $e->getMessage(), 'debug' => $linkedin->debugInfo,);
		}

		return $response;
	}

	/**
	 * Generic method to post a message
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function post($data)
	{
		// Set default variables
		$rt         = false;
		$post_state = 0;
		$post_id    = array();
		$post_error = array();

		// Prepare the data a bit more
		if (empty($data->title))
		{
			$data->title = $data->category_name;
		}

		// Loop through the accounts
		if (!empty($data->accounts))
		{
			foreach ($data->accounts as $account)
			{
				// Merge the data with the account
				$params        = YireoHelper::toRegistry($account->params);
				$account->page = $params->get('page');
				$account       = (object) array_merge((array) $account, (array) $data);
				unset($account->accounts);
				unset($account->account_id);

				// Post the data
				switch ($account->type)
				{
					case 'facebook':
						try
						{
							$response = $this->postFacebook($account);
						}
						catch (Exception $e)
						{
							$exception = $e->getMessage();
						}
						break;

					case 'linkedin':
						try
						{
							$response = $this->postLinkedin($account);
						}
						catch (Exception $e)
						{
							$exception = $e->getMessage();
						}
						break;

					default:
						try
						{
							$response = $this->postTwitter($account);
						}
						catch (Exception $e)
						{
							$exception = $e->getMessage();
						}
						break;
				}

				// Set the variables
				if (!empty($response['id']))
				{
					$post_state = 1;
					$post_id[]  = $response['id'];
					$rt         = true;

				}
				elseif (!empty($response['success']) && $response['success'] == 1)
				{
					$post_state = 1;
					$rt         = true;

				}
				else
				{
					if (!empty($response['error']))
					{
						$post_state   = 2;
						$post_error[] = $response['error'];

					}
					elseif (!empty($response['errors'][0]['message']))
					{
						$post_state   = 2;
						$post_error[] = $response['errors'][0]['message'];

					}
					elseif (!empty($exception))
					{
						$post_state   = 2;
						$post_error[] = 'Exception: ' . $exception;

					}
					else
					{
						$post_state   = 2;
						$post_error[] = 'Unknown response';
					}
				}
			}
		}

		// Update the state
		$this->updateState($data->id, $post_state, $post_id, $post_error);

		// Duplicate this tweet
		if ($rt == true && !empty($data->params))
		{
			$params     = YireoHelper::toRegistry($data->params);
			$reschedule = $params->get('reschedule');

			if (!empty($reschedule))
			{
				$this->duplicate($data, $reschedule);
			}
		}

		if (empty($response))
		{
			if (!empty($exception))
			{
				return array('error' => $e->getMessage());
			}
			else
			{
				return array('error' => 'Empty response');
			}
		}

		return $response;
	}

	/**
	 * @param      $id
	 * @param      $post_state
	 * @param      $post_id
	 * @param null $post_error
	 */
	public function updateState($id, $post_state, $post_id, $post_error = null)
	{
		$db         = JFactory::getDbo();
		$post_state = (int) $post_state;

		if (is_array($post_id))
		{
			$post_id = implode('|', $post_id);
		}

		if (is_array($post_error))
		{
			$post_error = implode('|', $post_error);
		}

		$post_id    = $db->quote($post_id);
		$post_error = $db->quote($post_error);

		$fields = array(
			$db->quoteName('post_state') . '=' . $post_state,
			$db->quoteName('post_id') . '=' . $post_id,
			$db->quoteName('post_error') . '=' . $post_error
		);

		$conditions = array($db->quoteName('id') . '=' . (int) $id);

		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__tweetscheduler_tweets'))
			->set($fields)
			->where($conditions);

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Method to duplicate a message
	 *
	 * @param array  $data
	 * @param string $reschedule
	 *
	 * @return boolean
	 */
	public function duplicate($data, $reschedule)
	{
		$model = new self;
		$model->setId(0);

		$old_post_date = $data->post_date;
		$new_post_date = TweetschedulerHelper::getRescheduleTime($old_post_date, $reschedule);

		$data->id        = 0;
		$data->post_date = $new_post_date;

		$data           = JArrayHelper::fromObject($data);
		$data['params'] = array('reschedule' => $reschedule,);

		return $model->store($data);
	}

	/**
	 * Method to automatically spread a set of tweets
	 *
	 * @param array $ids
	 *
	 * @return bool
	 */
	public function autospread($ids)
	{
		// Load the current tweets from the database and shuffle them
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(['id', 'category_id', 'message']));
		$query->from($db->quoteName('#__tweetscheduler_tweets'));
		$query->where($db->quoteName('published') . '=1');
		$query->where($db->quoteName('post_state') . '=1');
		$query->where($db->quoteName('id') . ' IN (' . implode(',', $ids) . ')');
		$db->setQuery($query);
		$tweets = $db->loadObjectList();
		shuffle($tweets);

		// @todo: Autospread options
		$minimumPerDay = 1;
		$maximumPerDay = 4;
		$startTime     = strtotime(date('Y-m-d'));
		$startTime     = $startTime + (60 * 60 * 24);

		$count         = 0;
		$averagePerDay = rand($minimumPerDay, $maximumPerDay);

		foreach ($tweets as $tweet)
		{
			// Add a day for every X posts
			if ($count % $averagePerDay == 0)
			{
				$averagePerDay = rand($minimumPerDay, $maximumPerDay);
				$startTime     = $startTime + (60 * 60 * 24);
			}

			// Randomize the time
			$randomSeconds = rand(0, (60 * 60 * 24));
			$post_date     = date('Y-m-d H:i:s', $startTime + $randomSeconds);

			// Update the tweet
			$object            = new stdClass;
			$object->post_date = $db->quote($post_date);
			$object->id        = (int) $tweet->id;
			$db->updateObject('#__tweetscheduler_tweets', $object, 'id');

			$count++;
		}

		return true;
	}
}
