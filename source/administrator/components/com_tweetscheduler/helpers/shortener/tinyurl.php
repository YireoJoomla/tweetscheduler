<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author    Yireo
 * @copyright Copyright 2016
 * @license   GNU Public License
 * @link      https://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Include the parent
require_once JPATH_COMPONENT . '/helpers/shortener.php';

/**
 * Class TweetschedulerHelperShortenerTinyurl
 */
class TweetschedulerHelperShortenerTinyurl extends TweetschedulerHelperShortener
{
	/**
	 * @var string
	 */
	protected $title = 'TinyURL';

	/**
	 * @param $url
	 *
	 * @return bool
	 */
	public function shorten($url)
	{
		if (preg_match('/http:\/\/(bit.ly|goo.gl|tinyurl.com)/', $url))
		{
			return $url;
		}

		$lookupUrl = 'http://tinyurl.com/api-create.php?url=' . $url;
		$data      = YireoHelper::fetchRemote($lookupUrl);

		return $data;
	}
}
