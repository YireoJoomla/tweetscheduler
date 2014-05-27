<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
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
        if(JRequest::getInt('modal') == 1) {
            echo '<script>window.parent.SqueezeBox.close();</script>';
            $this->app->close();
        }

        // Load scripts
        YireoHelper::jquery();
        $this->document->addScript( JURI::root().'media/com_tweetscheduler/js/backend.js' ) ;

        // Add clean-button to toolbar
        JToolBarHelper::custom('deletePosted','delete.png','delete.png', 'Clean posted', false);

        // Add autospread-button to toolbar
        JToolBarHelper::custom('autospread', 'copy.png', 'copy.png', 'Autospread', true, true);

        // Create select-filters
        $javascript = 'onchange="document.adminForm.submit();"';
        $this->lists['category_filter'] = JHTML::_('select.genericlist', TweetschedulerHelper::getCategoryOptions(true), 'filter_category_id', $javascript, 'value', 'title', $this->getFilter('category_id'));
        $this->lists['account_filter'] = JHTML::_('select.genericlist', TweetschedulerHelper::getAccountOptions(true), 'filter_account_id', $javascript, 'value', 'title', $this->getFilter('account_id'));
        $this->lists['post_state_filter'] = JHTML::_('select.genericlist', TweetschedulerHelper::getPostStateOptions(true), 'filter_post_state', $javascript, 'value', 'title', $this->getFilter('post_state'));
        $this->lists['state_filter'] = JHTML::_('select.genericlist', TweetschedulerHelper::getStateOptions(true), 'filter_state', $javascript, 'value', 'title', $this->getFilter('state'));

        // Add additional CSS
        $document = JFactory::getDocument();
        $document->addStylesheet('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');

        parent::display($tpl);
    }
}

