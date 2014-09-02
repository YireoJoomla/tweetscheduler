
<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo
 * @copyright Copyright (C) 2014
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Tweetscheduler Update Helper
 */
class TweetschedulerUpdate
{
    static public function runUpdateQueries()
    {
        // Collection of queries were going to try
        $update_queries = array (
            'ALTER TABLE `#__tweetscheduler_accounts` ADD `type` VARCHAR(50) NOT NULL AFTER `title`',
            'UPDATE `#__tweetscheduler_accounts` SET `type`="twitter" WHERE `type`=""',
            'ALTER TABLE `#__tweetscheduler_tweets` CHANGE `account_id` `account_id` VARCHAR(255) NOT NULL',
            'ALTER TABLE `#__tweetscheduler_tweets` ADD `title` VARCHAR(70) NOT NULL AFTER `category_id`',
            'ALTER TABLE `#__tweetscheduler_tweets` DROP COLUMN `ordering`',
            'ALTER TABLE `#__tweetscheduler_tweets` ADD `utc` TINYINT(1) NOT NULL DEFAULT 0 AFTER `post_error`',
        );

        // Perform all queries - we don't care if it fails
        $db = JFactory::getDBO();
        foreach( $update_queries as $query ) {
            try {
                $db->debug(0);
                $db->setQuery( $query );
                $db->query();
            } catch(Exception $e) {}
        }
    }
}

