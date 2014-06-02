<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @package Tweetscheduler
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!  
defined('_JEXEC') or die();

/**
 * HTML View class 
 *
 * @static
 * @package Tweetscheduler
 */
class TweetschedulerViewHome extends YireoViewHome
{
    /*
     * Display method
     *
     * @param string $tpl
     * @return null
     */
    public function display($tpl = null)
    {
        $icons = array();
        $icons[] = $this->icon( 'tweet&task=add', 'New Tweet', 'new.png', null );
        $icons[] = $this->icon( 'tweets', 'Tweets', 'tweet.png', null );
        $icons[] = $this->icon( 'accounts', 'Accounts', 'user.png', null );
        $icons[] = $this->icon( 'categories', 'Categories', 'category.png', null );
        $this->assignRef( 'icons', $icons );

        $urls = array();
        $urls['twitter'] ='http://twitter.com/yireo';
        $urls['facebook'] ='http://www.facebook.com/yireo';
        $urls['tutorials'] = 'http://www.yireo.com/tutorials/tweetscheduler';
        $urls['jed'] = 'http://extensions.joomla.org/extensions/social-web/social-auto-publish/16753';
        $this->assignRef( 'urls', $urls );

        JToolBarHelper::custom('updateQueries', 'archive', '', 'DB Upgrade', false);

        $graphdata = TweetschedulerHelper::getStatsData();
        $this->assignRef('graphdata', $graphdata);

        YireoHelper::jquery();
        $this->addJs('jquery.jqplot.min.js');
        $this->addJs('jqplot.dateAxisRenderer.min.js');
        $this->addCss('jquery.jqplot.css');
        $this->addCss('backend-graph.css');

        parent::display($tpl);
    }
}
