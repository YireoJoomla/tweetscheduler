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

class TweetschedulerHelperShortenerD404 extends TweetschedulerHelperShortener
{
    protected $_title = 'Dynamic404';

    static public function isEnabled($code)
    {
        if (file_exists(JPATH_ADMINISTRATOR.'/components/com_dynamic404/dynamic404.xml') == false) {
            return false;
        }

        return parent::isEnabled($code);
    }

    public function shorten($url)
    {
        if (preg_match('/http:\/\/(bit.ly|goo.gl|tinyurl.com)/', $url)) return $url;

        include_once JPATH_ADMINISTRATOR.'/components/com_dynamic404/helpers/helper.php';
        if (class_exists('Dynamic404Helper')) {
            $d404 = new Dynamic404Helper();
            if (method_exists($d404, 'generateShortUrl')) {
                return $d404->generateShortUrl($url);
            }
        }
    }
}
