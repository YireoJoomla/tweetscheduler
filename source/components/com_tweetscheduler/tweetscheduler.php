<?php
/**
 * Joomla! component TweetScheduler
 *
 * @author    Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license   GNU Public License
 * @link      http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Load the libraries
require_once JPATH_COMPONENT_ADMINISTRATOR . '/lib/loader.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php';

// Load common variables
$db = JFactory::getDBO();
$app = JFactory::getApplication();

// Set UTC timezone
$query = "SET time_zone = '+00:00'";
$db->setQuery($query);
$db->execute();

// Fetch the remaining tweets
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/tweets.php';
$model = new TweetSchedulerModelTweets();
$model->addWhere('tweet.post_date < NOW()');
$model->addWhere('tweet.post_state = 0');
$model->addWhere('tweet.published = 1');
$tweets = $model->getData();

// Fetch the singular model
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/tweet.php';
$tweetModel = new TweetSchedulerModelTweet();

// @todo: Make this configurable
$timeLimit = 60 * 60 * 24 * 14;
// @todo: Move this to the model
define('COM_TWEETSCHEDULER_POSTSTATE_STALLED', 3);

// Post the tweets
if (!empty($tweets))
{
	$count = 0;

	foreach ($tweets as $tweet)
	{
		if ($count > 2)
		{
			break;
		}

		// Stall tweets that are scheduled too long ago
		if (strtotime($tweet->post_date) < time() - $timeLimit)
		{
			$postError = JText::_('COM_TWEETSCHEDULER_ERROR_STALLED_TOO_LONG_AGO');
			$tweetModel->updateState($tweet->id, COM_TWEETSCHEDULER_POSTSTATE_STALLED, null, $postError);
		}

		TweetSchedulerHelper::post($tweet);
		$count++;
	}
}

$app->close();
// End