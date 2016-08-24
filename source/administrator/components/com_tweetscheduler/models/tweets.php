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
 * Tweetscheduler Tweets model
 */
class TweetschedulerModelTweets extends YireoModel
{
	/**
	 * Order-by default-value
	 *
	 * @var string
	 */
	protected $_orderby_default = 'post_date';

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct('tweet');

		$category_id = $this->getFilter('category_id');

		if (is_numeric($category_id) && $category_id > -1)
		{
			$this->addWhere('category_id = ' . (int) $category_id);
		}

		$account_id = $this->getFilter('account_id');

		if (is_numeric($account_id) && $account_id > -1)
		{
			$this->addWhere('FIND_IN_SET(' . (int) $account_id . ', account_id)');
		}

		$post_state = $this->getFilter('post_state');

		if (is_numeric($post_state) && $post_state > 0)
		{
			$this->addWhere('tweet.`post_state` = ' . (int) $post_state);
		}

		$state = $this->getFilter('state');

		if (is_numeric($state) && $state > -1)
		{
			$this->addWhere('tweet.`published` = ' . (int) $state);
		}

		$this->getConfig('search_fields', ['message']);
		$this->_orderby_default = 'post_date';
	}

	/**
	 * Override buildQuery method
	 *
	 * @param $query
	 *
	 * @return string
	 */
	protected function buildQuery($query = '')
	{
		$query = "SELECT {tableAlias}.*, category.title AS category_name, category.url AS category_url, editor.name AS editor FROM {table} AS {tableAlias} ";
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
			$accounts       = $this->getAccounts();
			$data->accounts = array();

			foreach ($accounts as $account)
			{
				if (in_array($account->id, $data->account_id))
				{
					$data->accounts[] = $account;
				}
			}
		}

		$data->raw_post_date = $data->post_date;

		if (isset($data->utc) && $data->utc == 1)
		{
			$timezone  = TweetschedulerHelper::getTimezone();
			$post_date = new JDate($data->post_date);
			$post_date->setTimezone($timezone);
			$data->post_date = $post_date->format('Y-m-d H:i', $timezone);
		}

		return $data;
	}

	/**
	 * Method to fetch a list of all accounts
	 *
	 * @return array
	 */
	protected function getAccounts()
	{
		static $accounts;

		if (!empty($accounts))
		{
			return $accounts;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__tweetscheduler_accounts'));
		$db->setQuery($query);
		$accounts = $db->loadObjectList();

		return $accounts;
	}
}
