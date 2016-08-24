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
 * Tweetscheduler Account model
 */
class TweetschedulerModelAccount extends YireoModel
{
	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct('account');
	}
}
