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

class TweetschedulerHelperShortener
{
    protected $_title = null;

    static public function autoshortenText($text)
    {
        // Match all the URL-strings in this text
        if (preg_match_all('/(http|https|ftp):\/\/([^\s]+)/', $text, $urls)) {
            foreach ($urls[0] as $url) {

                $newUrl = self::autoshortenUrl($url);

                // If the creation of a new shortened URL was succesful, replace it within the text
                if (!empty($newUrl) && $url != $newUrl) {
                    $text = str_replace($url, $newUrl, $text);
                }
            }
        }

        return $text; 
    }

    static public function autoshortenUrl($url)
    {
        // Don't modify anything unless the URL is longer than X characters
        if (strlen($url) < 30) return $url;

        // Loop through the shorteners, trying to shorten this URL
        $newUrl = null;
        $shorteners = self::getShorteners();
        foreach ($shorteners as $shortener) {
            $newUrl = $shortener->shorten($url);
            if (!empty($newUrl)) {
                return $newUrl;
            }
        }

        return $url;
    }

    static public function getShorteners()
    {
        $shorteners = array();
        foreach (self::getList() as $code) {
            $classFile = JPATH_COMPONENT.'/helpers/shortener/'.$code.'.php';
            $className = 'TweetschedulerHelperShortener'.ucfirst($code);

            if (file_exists($classFile)) {
                require_once $classFile;
                $object = new $className();

                if (method_exists($className, 'isEnabled') && $className::isEnabled($code) == false) {
                    continue;
                }

                if ($object->getTitle() == null) {
                    $object->setTitle($code);
                }

                $shorteners[$code] = $object;
            }
        }

        return $shorteners;
    }

    static public function getList()
    {
        return array(
            'bitly',
            'google',
            'tinyurl',
            'd404',
        );
    }

    static public function getParams()
    {
        $application = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        if ($application->isSite() == false) {
            return JComponentHelper::getParams($option);
        } else {
            return $application->getParams($option);
        }
    }

    static public function isEnabled($code)
    {
        $params = self::getParams();
        if ($params->get('enable_'.$code, 1) == 0) {
            return false;
        }
        return true;
    }

    /**
     * Method to fetch a specific page
     *
     * @access public
     * @param null
     * @return bool
     */
    public function postPage($url, $post_values = null)
    {
        // @todo: Implement file_get_contents()
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_values);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $contents = curl_exec($ch);
        $data = json_decode($contents, true);
        return $data['id'];
    }

    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function getTitle()
    {
        return $this->_title;
    }
}
