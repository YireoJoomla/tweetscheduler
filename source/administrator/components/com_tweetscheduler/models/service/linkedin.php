<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright Yireo.com 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

namespace Yireo\Tweetscheduler\Model\Service;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

include_once __DIR__ . '/contracts/data.php';
include_once __DIR__ . '/contracts/authorize.php';
include_once __DIR__ . '/generic.php';

/*
 * Tweetscheduler Linkedin service-model
 */
class Linkedin extends Generic implements Contracts\Authorize, Contracts\Data
{
	/**
	 * Authorize this service
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function authorize()
	{
		return true;
	}
}
