<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Include the parent
require_once JPATH_COMPONENT.'/helpers/shortener.php';

class TweetschedulerHelperShortenerBitly extends TweetschedulerHelperShortener
{
    protected $_title = 'bit.ly';

    public function shorten($url)
    {
        if (preg_match('/http:\/\/(bit.ly|goo.gl|tinyurl.com)/', $url)) return $url;

        $params = self::getParams();
        $username = $params->get('bitly_username');
        $apikey = $params->get('bitly_apikey');
        if (empty($username) || empty($apikey)) {
            return null;
        }
                
        $lookupUrl = 'http://api.bitly.com/v3/shorten?login='.$username.'&apiKey='.$apikey.'&longUrl='.rawurlencode($url).'&format=txt';
        $data = YireoHelper::fetchRemote($lookupUrl);
        return $data;
    }
}
