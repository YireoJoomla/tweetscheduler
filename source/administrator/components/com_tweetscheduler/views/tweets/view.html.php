<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class
 */
class TweetschedulerViewTweets extends YireoViewList
{
	/*
	 * Display method
	 *
	 * @param string $tpl
	 * @return null
	 */
	public function display($tpl = null)
	{
		// Hackish way of closing this page when it is a modal box
		if ($this->input->getInt('modal') == 1)
		{
			echo '<script>window.parent.SqueezeBox.close();</script>';
			$this->app->close();
		}

		// Load scripts
		YireoHelper::jquery();
		$this->doc->addScript(JUri::root() . 'media/com_tweetscheduler/js/backend.js');

		// Add clean-button to toolbar
		JToolbarHelper::custom('deletePosted', 'delete.png', 'delete.png', 'Clean posted', false);

		// Add autospread-button to toolbar
		JToolbarHelper::custom('autospread', 'copy.png', 'copy.png', 'Autospread', true, true);

		// Create select-filters
		$javascript                       = 'onchange="document.adminForm.submit();"';
		$this->lists['category_filter']   = JHtml::_('select.genericlist', TweetschedulerHelper::getCategoryOptions(true), 'filter_category_id', $javascript, 'value', 'title', $this->getFilter('category_id'));
		$this->lists['account_filter']    = JHtml::_('select.genericlist', TweetschedulerHelper::getAccountOptions(true), 'filter_account_id', $javascript, 'value', 'title', $this->getFilter('account_id'));
		$this->lists['post_state_filter'] = JHtml::_('select.genericlist', TweetschedulerHelper::getPostStateOptions(true), 'filter_post_state', $javascript, 'value', 'title', $this->getFilter('post_state'));
		$this->lists['state_filter']      = JHtml::_('select.genericlist', TweetschedulerHelper::getStateOptions(true), 'filter_state', $javascript, 'value', 'title', $this->getFilter('state'));

		// Add additional CSS
		$this->doc->addStyleSheet('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');

		parent::display($tpl);
	}
}

