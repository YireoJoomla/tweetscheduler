<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class
 */
class TweetschedulerViewCategory extends YireoViewForm
{
    /*
     * Display method
     *
     * @param string $tpl
     * @return null
     */
	public function display($tpl = null)
	{
        // Make sure the generic AJAX-function is loaded
        $this->getAjaxFunction();

        // Load the shorteners
        require_once JPATH_COMPONENT.'/helpers/shortener.php';
        $shorteners = TweetschedulerHelperShortener::getShorteners();
        $this->assignRef('shorteners', $shorteners);

		parent::display($tpl);
	}
}
