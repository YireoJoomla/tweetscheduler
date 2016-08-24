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

/*
 * Tweetscheduler Facebook service-model
 */
class Facebook extends Generic implements Contracts\Authorize, Contracts\Data
{
	/**
	 * Authorize this service
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function authorize()
	{
		$facebook = $this->getFacebookObject();
		$user = $this->getFacebookUser();

		// When there is an user, check if we are able to fetch its profile
		if ($user > 0)
		{
			try
			{
				$facebook->api('/me');
			}
			catch (\Exception $e)
			{
				$user = null;
			}
		}

		// If login failed, redirect to the login
		if (empty($user))
		{
			return $this->doRedirect();
		}

		// Fetch all permissions
		try
		{
			$permissions = $facebook->api("/me/permissions");
		}
		catch (\Exception $e)
		{
			$this->setMessage('Unable to fetch permissions');

			return false;
		}

		if ($this->checkIfPermissionsInclude($permissions, 'publish_actions') == false)
		{
			return $this->doRedirect('publish_actions');
		}

		if ($this->checkIfPermissionsInclude($permissions, 'manage_pages') == false)
		{
			return $this->doRedirect('manage_pages');
		}

		// Set the access-token
		$this->data->oauth_token = $facebook->getAccessToken();
		$this->data->oauth_token_secret = $facebook->getAppSecret();

		$this->setMessage('Account is authorized');

		return true;
	}

	/**
	 * Get the internal Facebook object
	 *
	 * @return \Facebook
	 */
	protected function getFacebookObject()
	{
		$facebook = \TweetschedulerHelper::getFacebook($this->getData());

		return $facebook;
	}

	/**
	 * Get the user from Facebook
	 *
	 * @return object
	 */
	protected function getFacebookUser()
	{
		// Initialize the Facebook-object
		$facebook = $this->getFacebookObject();

		// Fetch personal information
		$user = $facebook->getUser();

		return $user;
	}

	/**
	 * Redirect to another page
	 *
	 * @param null $scope
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function doRedirect($scope = null)
	{
		$data = array();

		if (!empty($scope))
		{
			$data = array('scope' => $scope);
		}

		$facebook = $this->getFacebookObject();
		$loginUrl = $facebook->getLoginUrl($data);

		return parent::redirect($loginUrl);
	}

	/**
	 * Check if the $permissions array contains a certain value
	 *
	 * @param $permissions
	 * @param $search
	 *
	 * @return bool
	 */
	protected function checkIfPermissionsInclude($permissions, $search)
	{
		if (empty($permissions['data']))
		{
			return false;
		}

		foreach($permissions['data'] as $permission)
		{
			if (array_key_exists($search, $permissions))
			{
				return true;
			}

			if (!empty($permission['permission']) && $permission['permission'] == $search)
			{
				return true;
			}
		}

		return false;
	}
}
