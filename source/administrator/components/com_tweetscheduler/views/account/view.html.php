<?php
/**
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
 * Account View class
 */
class TweetschedulerViewAccount extends YireoViewForm
{
	/**
	 * Display method
	 *
	 * @param string $tpl
	 * @return mixed
	 */
	public function display($tpl = null)
	{
		YireoHelper::jquery();

		$this->fetchItem();
		$this->lists['type'] = JHtml::_('select.genericlist', TweetschedulerHelper::getTypeOptions(), 'type', null, 'value', 'title', $this->item->type);

		$pages = array();

		if ($this->item->type == 'facebook')
		{
			try
			{
				$facebook = TweetschedulerHelper::getFacebook($this->item);
				$accounts = $facebook->api('/me/accounts');

				if (!empty($accounts['data']))
				{
					foreach ($accounts['data'] as $account)
					{
						$pages[$account['id']] = $account['name'];
					}
				}
			}
			catch (Exception $e)
			{
			}
		}

		$this->pages = $pages;

		return parent::display($tpl);
	}
}
