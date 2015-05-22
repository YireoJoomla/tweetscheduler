<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright Yireo.com 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

namespace Yireo\Tweetscheduler\Model\Service\Contracts;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/*
 * Tweetscheduler service-contract for authorizing social media
 */
interface Authorize
{
	/**
	 * Authorize this service
	 *
	 * @return bool
	 */
	public function authorize();

	/**
	 * Redirect to authorization URL after the first authorization has occurred
	 *
	 * @return bool
	 */
	public function redirectAuthorize();
}