<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo
 * @package Tweetscheduler
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!  
defined('_JEXEC') or die();

class com_tweetschedulerInstallerScript
{
	public function postflight($action, $installer)
	{
		switch ($action) {
			case 'install':
			case 'update':

                // Perform extra queries
                $db = JFactory::getDBO();
                $queries = array(
                );
                if (!empty($queries)) {
                    foreach ($queries as $query) {
                        $db->setQuery($query);
                        $db->query();
                    }
                }

                // Remove obsolete files
                $files = array(
                    JPATH_ADMINISTRATOR.'/components/com_tweetscheduler/views/home/tmpl/default.php',
                    JPATH_ADMINISTRATOR.'/components/com_tweetscheduler/views/home/tmpl/default_ads.php',
                    JPATH_ADMINISTRATOR.'/components/com_tweetscheduler/views/home/tmpl/default_cpanel.php',
                    JPATH_ADMINISTRATOR.'/components/com_tweetscheduler/views/home/tmpl/feeds.php',
                    JPATH_ADMINISTRATOR.'/components/com_tweetscheduler/views/tweets/tmpl/default.php',
                    JPATH_ADMINISTRATOR.'/components/com_tweetscheduler/views/accounts/tmpl/default.php',
                    JPATH_ADMINISTRATOR.'/components/com_tweetscheduler/views/categories/tmpl/default.php',
                );
                foreach($files as $file) {
                    if(file_exists($file)) @unlink($file);
                }

				break;
		}

        // Collection of queries were going to try
        $update_queries = array (
            'ALTER TABLE  `#__tweetscheduler_accounts` ADD  `type` VARCHAR( 50 ) NOT NULL AFTER  `title`',
            'UPDATE `#__tweetscheduler_accounts` SET `type`="twitter" WHERE `type`=""',
            'ALTER TABLE `#__tweetscheduler_tweets` CHANGE `account_id` `account_id` VARCHAR(255) NOT NULL',
            'ALTER TABLE `#__tweetscheduler_tweets` ADD `title` VARCHAR(70) NOT NULL AFTER `category_id`',
            'ALTER TABLE `#__tweetscheduler_tweets` DROP COLUMN `ordering`',
        );

        // Perform all queries - we don't care if it fails
        $db = JFactory::getDBO();
        foreach( $update_queries as $query ) {
            $db->debug(0);
            $db->setQuery( $query );
            $db->query();
        }
	}
}
