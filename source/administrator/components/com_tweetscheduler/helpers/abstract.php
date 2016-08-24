<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Tweetscheduler Structure
 */
class HelperAbstract
{
	/**
	 * Structural data of this component
	 */
	static public function getStructure()
	{
		return array(
			'title'          => 'Tweetscheduler',
			'menu'           => array(
				'home'       => 'Home',
				'tweets'     => 'Tweets',
				'accounts'   => 'Accounts',
				'categories' => 'Categories',
			),
			'views'          => array(
				'home'       => 'Home',
				'tweets'     => 'Tweets',
				'tweet'      => 'Tweet',
				'account'    => 'Account',
				'accounts'   => 'Accounts',
				'category'   => 'Category',
				'categories' => 'Categories',
			),
			'obsolete_files' => array(
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/views/home/tmpl/default.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/views/home/tmpl/default_ads.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/views/home/tmpl/default_cpanel.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/views/home/tmpl/feeds.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/views/accounts/tmpl/default.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/views/tweets/tmpl/default.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/views/categories/tmpl/default.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/lib/twitter/confirm.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/README.txt',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/lib/linkedin/linkedin_3.2.0.class.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/lib/linkedin/test_old.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/lib/linkedin/test.php',
				JPATH_ADMINISTRATOR . '/components/com_tweetscheduler/lib/facebook/test.php',
			),
		);
	}
}
