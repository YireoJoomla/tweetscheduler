<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author    Yireo
 * @copyright Copyright 2016
 * @license   GNU Public License
 * @link      https://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Class TweetschedulerHelper
 */
class TweetschedulerHelper
{
	/**
	 * Initialize the Twitter API
	 */
	static public function initTwitterApi()
	{
		// Include the Epi-libraries
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/lib/twitter/EpiCurl.php';
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/lib/twitter/EpiOAuth.php';
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/lib/twitter/EpiTwitter.php';
	}

	/**
	 * Initialize the Facebook API
	 */
	static public function initFacebookApi()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/lib/facebook/facebook.php';
	}

	/**
	 * Initialize the LinkedIn API
	 */
	static public function initLinkedinApi()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/lib/linkedin/linkedin.php';
	}

	/**
	 * Get the Twitter API object
	 *
	 * @param $data
	 *
	 * @return EpiTwitter
	 */
	static public function getTwitter($data)
	{
		// Initialize the API
		TweetschedulerHelper::initTwitterApi();

		// Initialize the twitter-object with the given data
		$twitter = new EpiTwitter($data->consumer_key, $data->consumer_secret, $data->oauth_token, $data->oauth_token_secret);

		return $twitter;
	}

	/**
	 * Get the Facebook API object
	 *
	 * @param $data
	 *
	 * @return Facebook
	 */
	static public function getFacebook($data)
	{
		// Initialize the API
		TweetschedulerHelper::initFacebookApi();

		// Initialize the facebook-object
		$facebook = new Facebook(array('appId' => $data->consumer_key, 'secret' => $data->consumer_secret,));

		if (!empty($data->oauth_token_secret))
		{
			$facebook->setAppSecret($data->oauth_token_secret);
		}
		if (!empty($data->oauth_token))
		{
			$facebook->setAccessToken($data->oauth_token);
		}

		return $facebook;
	}

	/**
	 * Get the LinkedIn API object
	 *
	 * @param $data
	 *
	 * @return LinkedIn
	 * @throws LinkedInException
	 */
	static public function getLinkedin($data)
	{
		// Initialize the API
		TweetschedulerHelper::initLinkedinApi();

		$config = array(
			'appKey'      => $data->consumer_key,
			'appSecret'   => $data->consumer_secret,
			'callbackUrl' => $_SERVER['REQUEST_URI'],
		);

		$linkedin = new LinkedIn($config);
		$linkedin->setResponseFormat('JSON');
		$response = $linkedin->retrieveTokenRequest();

		//$oauth = new OAuthConsumer($data->consumer_key, $data->consumer_secret);
		$linkedin->setToken(array(
			'oauth_token'        => $data->oauth_token,
			'oauth_token_secret' => $data->oauth_token_secret
		));

		return $linkedin;
	}

	/**
	 * Shortcut method to post a tweet
	 *
	 * @param $data
	 *
	 * @return string
	 */
	static public function post($data)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/models/tweet.php';
		$model = new TweetschedulerModelTweet();

		return $model->post($data);
	}

	/**
	 * Fetch a list of categories
	 *
	 * @param boolean $include_null
	 *
	 * @return array
	 */
	static public function getCategoryOptions($include_null = false)
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT `title`,`id` AS `value`,`url`,`params` FROM #__tweetscheduler_categories ORDER BY ordering');
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$row->params = YireoHelper::toParameter($row->params);
		}

		$option = (object) array(
			'title'  => '-- ' . JText::_('JNONE') . ' --',
			'value'  => 0,
			'url'    => null,
			'params' => null,
		);
		array_unshift($rows, $option);

		if ($include_null)
		{
			$option = (object) array('title' => '-- ' . JText::_('JSELECT') . ' --', 'value' => -1);
			array_unshift($rows, $option);
		}

		return $rows;
	}

	/**
	 * Fetch a list of accounts
	 *
	 * @param boolean $include_null
	 *
	 * @return array
	 */
	static public function getAccountOptions($include_null = false, $include_none = true)
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT `title`, `type`, `id` AS `value` FROM #__tweetscheduler_accounts WHERE `published`=1 ORDER BY ordering');
		$rows = $db->loadObjectList();
		foreach ($rows as $rowIndex => $row)
		{
			$row->title      = $row->title . ' [' . $row->type . ']';
			$rows[$rowIndex] = $row;
		}

		$option = (object) array('title' => '-- ' . JText::_('JNONE') . ' --', 'value' => 0);
		array_unshift($rows, $option);

		if ($include_null)
		{
			$option = (object) array('title' => '-- ' . JText::_('JSELECT') . ' --', 'value' => -1);
			array_unshift($rows, $option);
		}

		if ($include_null)
		{
			$option = (object) array('title' => '-- ' . JText::_('JSELECT') . ' --', 'value' => -1);
			array_unshift($rows, $option);
		}

		return $rows;
	}

	/**
	 * Return the default account ID
	 *
	 * @return int
	 */
	static public function getDefaultAccountId()
	{
		$accounts = self::getAccounts();

		foreach ($accounts as $account)
		{
			$params = new JRegistry($account->params);

			if ($params->get('default', 0) == 1)
			{
				return $account->id;
			}
		}

		return 0;
	}

	/**
	 * Select all accounts as an array of objects
	 *
	 * @return array
	 */
	static public function getAccounts()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'title', 'type', 'params')))
			->from($db->quoteName('#__tweetscheduler_accounts'))
			->order($db->quoteName('ordering'));

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Fetch a list of types
	 *
	 * @param boolean $include_null
	 *
	 * @return array
	 */
	static public function getTypeOptions($include_null = false)
	{
		$rows = array(
			array('title' => JText::_('Twitter'), 'value' => 'twitter'),
			array('title' => JText::_('Facebook'), 'value' => 'facebook'),
			array('title' => JText::_('LinkedIn'), 'value' => 'linkedin'),
		);

		if ($include_null)
		{
			$option = array('title' => '-- Select --', 'value' => null);
			array_unshift($rows, $option);
		}

		return $rows;
	}

	/**
	 * Fetch a list of states
	 *
	 * @param null
	 *
	 * @return null
	 */
	static public function getStateOptions()
	{
		$rows = array(
			array('title' => JText::_('Any'), 'value' => -1),
			array('title' => JText::_('Unpublished'), 'value' => 0),
			array('title' => JText::_('Published'), 'value' => 1),
		);

		return $rows;
	}

	/**
	 * Fetch a list of accounts
	 *
	 * @param string $tpl
	 *
	 * @return null
	 */
	static public function getPostStateOptions($include_null = false)
	{
		$rows = array(
			array('title' => JText::_('COM_TWEETSCHEDULER_FIELDNAME_POST_STATE_0'), 'value' => 0),
			array('title' => JText::_('COM_TWEETSCHEDULER_FIELDNAME_POST_STATE_1'), 'value' => 1),
			array('title' => JText::_('COM_TWEETSCHEDULER_FIELDNAME_POST_STATE_2'), 'value' => 2),
			array('title' => JText::_('COM_TWEETSCHEDULER_FIELDNAME_POST_STATE_3'), 'value' => 3),
		);

		if ($include_null)
		{
			$option = array('title' => '-- Select --', 'value' => null);
			array_unshift($rows, $option);
		}

		return $rows;
	}

	/**
	 * Method to return the extra seconds for a specific string
	 *
	 * @param mixed $timestring
	 *
	 * @return string
	 */
	static public function getRescheduleTime($current_time, $reschedule_time)
	{
		if (preg_match('/^([0-9]+)([a-z]+)/', $reschedule_time, $match))
		{
			$new_time = strtotime('+' . $match[1] . ' ' . $match[2], strtotime($current_time));

			return date('Y-m-d H:i:s', $new_time);
		}

		return $current_time;
	}

	/**
	 * Method to format the time
	 *
	 * @param mixed $timestring
	 *
	 * @return string
	 */
	static public function getRelativeTime($time, $utc = 1)
	{
		$utc = (bool) $utc;

		$timezone  = self::getTimezone();
		$datetime  = new JDate($time);
		$timestamp = strtotime($datetime->format('r'));

		$seconds = $timestamp - time();

		$time_string = null;
		if ($seconds == 0)
		{
			$time_string = 'now';
		}
		elseif ($seconds > 0)
		{
			$minutes = round($seconds / 60);
			$hours   = round($seconds / 60 / 60);
			$days    = round($seconds / 60 / 60 / 24);
			if ($minutes < 2)
			{
				$time_string = $minutes . ' minute';
			}
			elseif ($minutes < 60)
			{
				$time_string = $minutes . ' minutes';
			}
			elseif ($hours == 1)
			{
				$time_string = $hours . ' hour';
			}
			elseif ($hours < 24)
			{
				$time_string = $hours . ' hours';
			}
			elseif ($days == 1)
			{
				$time_string = $days . ' day';
			}
			else
			{
				$time_string = $days . ' days';
			}
		}
		else
		{
			$minutes = round((0 - $seconds) / 60);
			$hours   = round((0 - $seconds) / 60 / 60);
			$days    = round((0 - $seconds) / 60 / 60 / 24);
			if ($minutes < 2)
			{
				$time_string = $minutes . ' minute ago';
			}
			elseif ($minutes < 60)
			{
				$time_string = $minutes . ' minutes ago';
			}
			elseif ($hours == 1)
			{
				$time_string = $hours . ' hour ago';
			}
			elseif ($hours < 24)
			{
				$time_string = $hours . ' hours ago';
			}
			elseif ($days == 1)
			{
				$time_string = $days . ' day ago';
			}
			else
			{
				$time_string = $days . ' days ago';
			}
		}

		return $time_string;
	}

	/**
	 * Wrapper to format a date / time string
	 *
	 * @param $datetime
	 *
	 * @return string
	 */
	static public function formatDatetime($datetime)
	{
		return JFactory::getDate($datetime)
			->format(JText::_('DATE_FORMAT_LC2'));
	}

	/**
	 * Wrapper to get the current timezone
	 *
	 * @return DateTimeZone
	 */
	static public function getTimezone()
	{
		$timezone = JFactory::getUser()
			->getParam('timezone');
		if (empty($timezone))
		{
			$timezone = JFactory::getConfig()
				->get('offset');
		}

		return new DateTimeZone($timezone);
	}
}
