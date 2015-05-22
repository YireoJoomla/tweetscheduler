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
class TweetschedulerViewTweet extends YireoViewForm
{
	/*
	 * Display method
	 *
	 * @param string $tpl
	 * @return null
	 */
	public function display($tpl = null)
	{
		// Load jQuery
		YireoHelper::jquery();

		// Load additional scripts
		$this->document->addScript(JURI::root() . 'media/com_tweetscheduler/js/backend.js');

		// Load the item
		$this->fetchItem();

		// Overload settings using article parameters
		$this->loadArticle();

		// Build the fields
		if (!$this->item->category_id > 0)
		{
			$this->item->category_id = $this->getFilter('category_id', null, null, 'com_tweetscheduler_tweets_');
		}

		if (empty($this->item->account_id))
		{
			$this->item->account_id = TweetschedulerHelper::getDefaultAccountId();
		}

		$options = TweetschedulerHelper::getCategoryOptions(true);
		$this->lists['category_id'] = JHTML::_('select.genericlist', $options, 'category_id', null, 'value', 'title', $this->item->category_id);

		$options = TweetschedulerHelper::getAccountOptions();
		$this->lists['account_id'] = JHTML::_('select.genericlist', $options, 'account_id[]', 'multiple="multiple"', 'value', 'title', $this->item->account_id);

		$this->lists['post_date'] = JHTML::_('calendar', $this->item->post_date, 'post_date', 'post_date', '%Y-%m-%d %H:%M:%S', array('class' => 'inputbox'));

		$this->lists['categories'] = TweetschedulerHelper::getCategoryOptions();

		parent::display($tpl);
	}

	/*
	 * Load article information
	 *
	 * @param string $tpl
	 * @return null
	 */
	public function loadArticle()
	{
		// Check for the right parameters
		$form = JRequest::getCmd('formname');
		$asset = JRequest::getInt('asset');

		if ($form != 'jform_articletext' || $asset > 0 == false)
		{
			return false;
		}

		// Load the article
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('a.`id`', 'a.`title`', 'a.`alias`', 'a.`introtext`', 'a.`catid`', 'c.`alias` AS catalias'));
		$query->from('`#__content` AS a');
		$query->join('inner', '#__categories AS c ON c.id = a.catid');
		$query->where($db->quoteName('a.asset_id') . ' = ' . $asset);
		$db->setQuery($query);

		$article = $db->loadObject();

		if (empty($article))
		{
			return false;
		}

		// Enter the details
		$this->item->title = $article->title;
		$slug = $article->id . ':' . $article->alias;
		$catslug = $article->catid . ':' . $article->catalias;;
		$link = $this->getFrontendUrl($slug, $catslug);

		require_once JPATH_COMPONENT . '/helpers/shortener.php';
		$link = TweetschedulerHelperShortener::autoshortenUrl($link);

		// Construct the message
		$this->item->message = $this->getMessageText($article->title, $article->introtext, $link);

		return true;
	}

	public function getMessageText($title, $introtext, $url)
	{
		// Fetch parameters
		$params = JComponentHelper::getComponent('com_tweetscheduler')->params;
		$article_parts = $params->get('article_parts');
		$introtext = strip_tags($introtext);

		// Maximum chars in 1 tweet
		$maxChars = 140;

		// Estimation of chars in shortened URL
		$urlChars = 30;

		// Setup the prefix & suffix
		$prefix = trim($params->get('article_prefix'));
		$suffix = trim($params->get('article_suffix'));

		// Calculate the available characters
		$charCount = 0;

		if (!empty($prefix))
		{
			$charCount += strlen($prefix) + 1;
		}

		if (!empty($suffix))
		{
			$charCount += strlen($suffix) + 1;
		}

		if (!empty($url))
		{
			$charCount += strlen($url) + 1;
		}

		// Switch for the right ordering
		$availableChars = $maxChars - $charCount - 2;

		if ($article_parts == 'tu')
		{
			$title = substr($title, 0, $availableChars);
			$message = $prefix . ' ' . $title . ' ' . $url . ' ' . $suffix;
		}
		elseif ($article_parts == 'ut')
		{
			$title = substr($title, 0, $availableChars);
			$message = $prefix . ' ' . $url . ' ' . $title . ' ' . $suffix;
		}
		elseif ($article_parts == 'bu')
		{
			$introtext = substr($introtext, 0, $availableChars);
			$message = $prefix . ' ' . $url . ' ' . $introtext . ' ' . $suffix;
		}
		elseif ($article_parts == 'ub')
		{
			$introtext = substr($introtext, 0, $availableChars);
			$message = $prefix . ' ' . $introtext . ' ' . $url . ' ' . $suffix;
		}

		$message = trim($message);

		return $message;
	}

	public function getFrontendUrl($slug, $catslug)
	{
		require_once JPATH_SITE . '/components/com_content/helpers/route.php';
		$url = ContentHelperRoute::getArticleRoute($slug, $catslug);

		JFactory::$application = JApplication::getInstance('site');
		$app = JApplication::getInstance('site');

		$router = $app->getRouter();
		$uri = $router->build($url);
		$url = $uri->toString(array('path', 'query', 'fragment'));
		$url = str_replace('/administrator/', '', $url);
		$url = JURI::root() . $url;

		JFactory::$application = JApplication::getInstance('administrator');

		return $url;
	}
}
