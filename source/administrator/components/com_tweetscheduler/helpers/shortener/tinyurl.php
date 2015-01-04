<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Include the parent
require_once JPATH_COMPONENT.'/helpers/shortener.php';

class TweetschedulerHelperShortenerTinyurl extends TweetschedulerHelperShortener
{
    protected $_title = 'TinyURL';

    public function shorten($url)
    {
        if (preg_match('/http:\/\/(bit.ly|goo.gl|tinyurl.com)/', $url)) return $url;

        $lookupUrl = 'http://tinyurl.com/api-create.php?url='.$url;
        $data = YireoHelper::fetchRemote($lookupUrl);
        return $data;
    }
}
