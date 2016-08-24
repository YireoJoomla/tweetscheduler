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
 * Class TweetschedulerHelperShortenerGoogle
 */
class TweetschedulerHelperShortenerGoogle extends TweetschedulerHelperShortener
{
	/**
	 * @var string
	 */
	protected $title = 'Google';

	/**
	 * @param $url
	 *
	 * @return mixed
	 */
	public function shorten($url)
	{
		if (preg_match('/(http|https):\/\/(bit.ly|goo.gl|tinyurl.com)/', $url))
		{
			return $url;
		}

		$params = self::getParams();
		$apikey = $params->get('google_api_key');

		if (!empty($apikey))
		{
			$lookupUrl = 'https://www.googleapis.com/urlshortener/v1/url?key=' . $apikey;
			$data      = self::postPage($lookupUrl, json_encode(array('longUrl' => $url)));

			return $data;
		}
	}
}
