<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright Yireo.com 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// Check to ensure this file is included in Joomla
defined('_JEXEC') or die();

/**
 * Tweetscheduler Accounts model
 */
class TweetschedulerModelAccounts extends YireoModel
{
	/**
	 * Constructor method
	 */
	public function __construct()
	{
		$this->search = array('title');
		parent::__construct('account');
	}
}
