<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author    Yireo (info@yireo.com)
 * @package   Tweetscheduler
 * @copyright Copyright 2016
 * @license   GNU Public License
 * @link      https://www.yireo.com
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
	/**
	 * Display method
	 *
	 * @param string $tpl
	 *
	 * @return null
	 */
	public function display($tpl = null)
	{
		$sourceUrl     = $this->app->input->getString('url');
		$shortenerCode = $this->app->input->getString('shortener');
		$newUrl        = null;

		require_once JPATH_COMPONENT . '/helpers/shortener.php';
		$shorteners = TweetschedulerHelperShortener::getShorteners();

		if (isset($shorteners[$shortenerCode]))
		{
			$shortener = $shorteners[$shortenerCode];

			if (!empty($shortener))
			{
				print $shortener->shorten($sourceUrl);
			}
		}

		$this->app->close();
		exit;
	}
}
