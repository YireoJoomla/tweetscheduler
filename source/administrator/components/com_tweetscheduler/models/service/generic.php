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

/*
 * Tweetscheduler abstract service-model
 */
class Generic  implements Contracts\Authorize, Contracts\Data
{
	/**
	 * Data to authorize with
	 *
	 * @var
	 */
	protected $data;

	/**
	 * Feedback message
	 *
	 * @var string
	 */
	protected $message = null;

	/**
	 * Feedback message type
	 *
	 * @var string
	 */
	protected $messageType = 'notice';

	/**
	 * Set the data
	 *
	 * @param $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * Get the data
	 *
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Set the message
	 *
	 * @param      $message
	 * @param string $messageType
	 */
	public function setMessage($message, $messageType = null)
	{
		if (!empty($messageType))
		{
			$this->messageType = $messageType;
		}

		$this->message = $message;
	}

	/**
	 * Get the message
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Get the message type
	 *
	 * @return string
	 */
	public function getMessageType()
	{
		return $this->messageType;
	}

	/**
	 * Authorize this service
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return false;
	}

	/**
	 * Redirect to authorization URL after the first authorization has occurred
	 *
	 * @return bool
	 */
	public function redirectAuthorize()
	{
		return false;
	}

	/**
	 * Redirect to another page
	 *
	 * @param null $scope
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function redirect($url = null)
	{
		\JFactory::getApplication()->redirect($url);
		\JFactory::getApplication()->close();

		return true;
	}
}