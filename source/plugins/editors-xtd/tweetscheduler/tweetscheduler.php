<?php
/*
 * Joomla! Editor Button Plugin - TweetScheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * TweetScheduler Editor Button Plugin
 */
class plgButtonTweetScheduler extends JPlugin
{
    /**
     * Method to display the button
     *
     * @param string $name
     */
    public function onDisplay($name, $asset, $author)
    {
        // Load the parameters
        $params = JComponentHelper::getParams('com_tweetscheduler');

        // Construct the button
        $link = 'index.php?option=com_tweetscheduler&amp;view=tweet&amp;modal=1&amp;tmpl=component&amp;formname='.$name.'&amp;asset='.$asset;
		JHtml::_('behavior.modal');
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
        $button->set('class', 'btn');
		$button->set('text', 'TweetScheduler');
		$button->set('name', 'image');
		$button->set('options', "{handler: 'iframe', size: {x: 800, y: 600}}");

		return $button;
    }
}
