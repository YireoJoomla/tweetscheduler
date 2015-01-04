<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @package Tweetscheduler
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!  
defined('_JEXEC') or die();

/**
 * HTML View class 
 *
 * @static
 * @package Tweetscheduler
 */
class TweetschedulerViewCategory extends YireoView
{
    /*
     * Display method
     *
     * @param string $tpl
     * @return null
     */
    public function display($tpl = null)
    {
        $sourceUrl = JRequest::getString('url');
        $shortenerCode = JRequest::getString('shortener');
        $newUrl = null;

        require_once JPATH_COMPONENT.'/helpers/shortener.php';
        $shorteners = TweetschedulerHelperShortener::getShorteners();
        if(isset($shorteners[$shortenerCode])) {
            $shortener = $shorteners[$shortenerCode];
            if(!empty($shortener)) {
                print $shortener->shorten($sourceUrl);
            }
        }

        $application = JFactory::getApplication();
        $application->close();
        exit;
    }
}
