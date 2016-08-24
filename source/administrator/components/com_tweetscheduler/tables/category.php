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
 * Category Table class
 */
class TweetschedulerTableCategory extends YireoTable
{
	/**
	 * Constructor
	 *
	 * @param JDatabase $db
	 */
	public function __construct(& $db)
	{
		// Initialize the fields
		$this->_fields = array(
			'id'    => null,
			'title' => null,
			'url'   => null,
		);

		// Set the required fields
		$this->_required = array(
			'title',
		);

		// Call the constructor
		parent::__construct('#__tweetscheduler_categories', 'id', $db);
	}
}
