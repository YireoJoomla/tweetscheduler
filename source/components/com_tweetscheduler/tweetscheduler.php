<?php
/**
 * Joomla! component TweetScheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Load the libraries
require_once JPATH_COMPONENT_ADMINISTRATOR.'/lib/loader.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';
        
// Load common variables
$db = JFactory::getDBO();
$app = JFactory::getApplication();

// Set UTC timezone
$query = "SET time_zone = '+00:00'";
$db->setQuery($query);
$db->query();

// Fetch the remaining tweets
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/tweets.php';
$model = new TweetSchedulerModelTweets();
$model->addWhere('post_date < NOW()');
$model->addWhere('post_state = 0');
$tweets = $model->getData();

// Post the tweets
if (!empty($tweets)) {
    $count = 0;
    foreach ($tweets as $tweet) {
        if($count > 2) break;
        TweetSchedulerHelper::post($tweet);
        $count++;
    }
}

$app->close();
