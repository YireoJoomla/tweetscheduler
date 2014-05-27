<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright Yireo.com 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/*
 * Tweetscheduler Category model
 */
class TweetschedulerModelCategory extends YireoModel
{
    /**
     * Constructor method
     *
     * @access public
     * @param null
     * @return null
     */
    public function __construct()
    {
        parent::__construct('category');
    }

    /**
     * Method to store the model
     *
     * @access public
     * @subpackage Yireo
     * @param mixed $data
     * @return bool
     */
    public function store($data)
    {
        if($this->params->get('autoshorten', 1) == 1 && !empty($data['url'])) {
            require_once JPATH_COMPONENT.'/helpers/shortener.php';
            $data['url'] = trim(TweetschedulerHelperShortener::autoshortenUrl($data['url']));
        }

        return parent::store($data);
    }
}
