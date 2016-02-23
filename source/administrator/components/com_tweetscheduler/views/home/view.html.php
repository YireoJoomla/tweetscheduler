<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @package Tweetscheduler
 * @copyright Copyright 2015
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

        $this->graphdays = 30;
        $this->graphdata = $this->getGraphData($this->graphdays);

        parent::display($tpl);
    }

    public function getGraphData($count)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('DATE(post_date) AS date');
        $query->from('#__tweetscheduler_tweets');
        $query->where($db->quoteName('published').'=1');
        $query->where($db->quoteName('post_date').' BETWEEN NOW() AND
NOW() + INTERVAL '.$count.' DAY');
        $query->orderby($db->quoteName('post_date'));

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $data = array();

        foreach($rows as $row) {
            if (!isset($data[$row->date])) {
                $data[$row->date] = 0;
            }

            $data[$row->date]++;
        }

        $days = array();
        $days[] = array('Day', 'Count');

        for($i = 0; $i < 30; $i++) {
            $day = date('Y-m-d', strtotime('+'.$i . ' day'));
            if (isset($data[$day])) {
                $days[] = array($day, $data[$day]);
            } else {
                $days[] = array($day, 0);
            }
        }

        return $days;
    }
}
