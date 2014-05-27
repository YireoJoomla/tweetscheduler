<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Account Table class
*/
class TableAccount extends YireoTable
{
    /**
     * Constructor
     *
     * @access public
     * @param JDatabase $db
     * @return null
     */
    public function __construct(& $db)
    {
        // Initialize the fields
        $this->_fields = array(
            'id' => null,
            'title' => null,
            'consumer_key' => null,
            'consumer_secret' => null,
            'oauth_token' => null,
            'oauth_token_secret' => null,
        );

        // Set the required fields
        $this->_required = array(
            'title',
            'consumer_key',
            'consumer_secret',
        );

        // Call the constructor
        parent::__construct('#__tweetscheduler_accounts', 'id', $db);
    }
}
