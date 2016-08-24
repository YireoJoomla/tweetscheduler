<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author    Yireo
 * @copyright Copyright 2016
 * @license   GNU Public License
 * @link      https://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Class TweetschedulerHelperShortener
 */
class TweetschedulerHelperShortener
{
	/**
	 * @var string
	 */
	protected $title = null;

	/**
	 * @return TweetschedulerHelperShortener
	 */
	static public function getInstance()
	{
		return new self;
	}

	/**
	 * @param $text
	 *
	 * @return mixed
	 */
	static public function autoshortenText($text)
	{
		// Match all the URL-strings in this text
		if (preg_match_all('/(http|https|ftp):\/\/([^\s]+)/', $text, $urls))
		{
			foreach ($urls[0] as $url)
			{
				$newUrl = self::autoshortenUrl($url);

				// If the creation of a new shortened URL was succesful, replace it within the text
				if (!empty($newUrl) && $url != $newUrl)
				{
					$text = str_replace($url, $newUrl, $text);
				}
			}
		}

		return $text;
	}

	/**
	 * @param $url
	 *
	 * @return null
	 */
	static public function autoshortenUrl($url)
	{
		// Don't modify anything unless the URL is longer than X characters
		if (YireoHelper::strlen($url) < 30)
		{
			return $url;
		}

		// Loop through the shorteners, trying to shorten this URL
		$newUrl     = null;
		$shorteners = self::getShorteners();

		foreach ($shorteners as $shortener)
		{
			$newUrl = $shortener->shorten($url);

			if (!empty($newUrl))
			{
				return $newUrl;
			}
		}

		return $url;
	}

	/**
	 * @return array
	 */
	static public function getShorteners()
	{
		$shorteners = array();

		foreach (self::getList() as $code)
		{
			$classFile = JPATH_COMPONENT . '/helpers/shortener/' . $code . '.php';
			$className = 'TweetschedulerHelperShortener' . ucfirst($code);

			if (file_exists($classFile))
			{
				require_once $classFile;
				$object = new $className;

				if (method_exists($className, 'isEnabled') && $className::isEnabled($code) == false)
				{
					continue;
				}

				if ($object->getTitle() == null)
				{
					$object->setTitle($code);
				}

				$shorteners[$code] = $object;
			}
		}

		return $shorteners;
	}

	/**
	 * @return array
	 */
	static public function getList()
	{
		return array(
			'bitly',
			'google',
			'tinyurl',
			'd404',
		);
	}

	/**
	 * @return \Joomla\Registry\Registry
	 */
	static public function getParams()
	{
		$application = JFactory::getApplication();
		$option      = $application->input->getCmd('option');

		if ($application->isSite() === false)
		{
			return JComponentHelper::getParams($option);
		}

		return $application->getParams($option);
	}

	/**
	 * @param $code
	 *
	 * @return bool
	 */
	static public function isEnabled($code)
	{
		$params = self::getParams();

		if ($params->get('enable_' . $code, 1) == 0)
		{
			return false;
		}

		return true;
	}

	/**
	 * @param      $url
	 * @param null $post_values
	 *
	 * @return mixed
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
		$data     = json_decode($contents, true);

		return $data['id'];
	}

	/**
	 * @param $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return null
	 */
	public function getTitle()
	{
		return $this->title;
	}
}
