<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Tweet Table class
 */
class TweetschedulerTableTweet extends YireoTable
{
	/**
	 * @var string
	 */
	public $message;

	/**
	 * @var int
	 */
	public $category_id;

	/**
	 * Constructor
	 *
	 * @param JDatabase $db
	 */
	public function __construct(& $db)
	{
		// Initialize the fields
		$timezone  = TweetschedulerHelper::getTimezone();
		$post_date = new JDate('now +2 hours', $timezone);

		$this->_defaults = array(
			'post_date' => $post_date->format('Y-m-d H:i'),
			'utc'       => 1,
		);

		// Set the required fields
		$this->_required = array(
			'message',
			'account_id',
			'category_id',
		);

		// Call the constructor
		parent::__construct('#__tweetscheduler_tweets', 'id', $db);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 */
	public function check()
	{
		// Perform the parent-checks
		$result = parent::check();

		if ($result === false)
		{
			return false;
		}

		// Append the category URL to this message
		if ($this->category_id)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('url'));
			$query->from($db->quoteName('#__tweetscheduler_categories'));
			$query->where($db->quoteName('id') . '=' . (int) $this->category_id);
			$db->setQuery($query);
			$category_url = $db->loadResult();

			if (!empty($category_url) && !strstr($this->message, $category_url))
			{
				$this->message .= ' ' . $category_url;
			}
		}

		// Check whether the message does not exceed the maximum
		$too_many_chars = 140 - YireoHelper::strlen($this->message);

		if ($too_many_chars < 0)
		{
			throw new Exception(sprintf('Message exceeds maximum length by %d characters', 0 - $too_many_chars));
		}

		return true;
	}
}
