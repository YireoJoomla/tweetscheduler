<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright Yireo.com 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

namespace Yireo\Tweetscheduler\Model\Service;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

include_once __DIR__ . '/contracts/data.php';
include_once __DIR__ . '/contracts/authorize.php';
include_once __DIR__ . '/generic.php';

/**
 * Tweetscheduler Twitter service-model
 */
class Twitter extends Generic implements Contracts\Authorize, Contracts\Data
{
	/**
	 * Twitter object
	 */
	protected $twitter = null;

	/**
	 * Authorize this service
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function authorize()
	{
		$twitter = $this->getTwitterObject();

		// Get the request-token
		try
		{
			$token            = $twitter->getRequestToken();
			$this->twitterUrl = $twitter->getAuthorizationUrl($token);
		}
		catch (\Exception $e)
		{
			$this->setMessage($e->getMessage(), 'error');

			return false;
		}

		// Save the token in the model
		$this->data->oauth_token        = $token->oauth_token;
		$this->data->oauth_token_secret = $token->oauth_token_secret;

		return true;
	}

	/**
	 * Redirect to authorization URL after the first authorization has occurred
	 *
	 * @return bool
	 */
	public function redirectAuthorize()
	{
		// Redirect to twitter authorization
		return $this->redirect($this->twitterUrl);
	}

	/**
	 * Get Twitter API object
	 */
	protected function getTwitterObject()
	{
		if (!empty($this->twitter))
		{
			return $this->twitter;
		}

		\TweetschedulerHelper::initTwitterApi();

		try
		{
			$this->twitter = new \EpiTwitter($this->data->consumer_key, $this->data->consumer_secret);
		}
		catch (\Exception $e)
		{
			$this->msg = $e->getMessage();

			return false;
		}

		return $this->twitter;
	}
}
