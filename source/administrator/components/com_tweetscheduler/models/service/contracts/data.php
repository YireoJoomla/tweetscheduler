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
 * Tweetscheduler service-contract for acting as a data-container
 */
interface Data
{
	/**
	 * Set the data
	 *
	 * @param $data
	 */
	public function setData($data);

	/**
	 * Get the data
	 *
	 * @return mixed
	 */
	public function getData();

	/**
	 * Set the message
	 *
	 * @param        $message
	 * @param string $messageType
	 */
	public function setMessage($message, $messageType = null);

	/**
	 * Get the message
	 *
	 * @return string
	 */
	public function getMessage();

	/**
	 * Get the message type
	 *
	 * @return string
	 */
	public function getMessageType();
}