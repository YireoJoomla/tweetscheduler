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
 * Tweetscheduler Tweets model
 */
class TweetschedulerModelTweets extends YireoModel
{
    /**
     * Order-by default-value
     *
     * @protected string
     */
    protected $_orderby_default = 'post_date';

    /**
     * Constructor method
     *
     * @access public
     * @param null
     * @return null
     */
    public function __construct()
    {
        //$this->_debug = true;
        parent::__construct('tweet');

        $category_id = $this->getFilter('category_id');
        if(is_numeric($category_id) && $category_id > -1) {
            $this->addWhere('category_id = '.(int)$category_id);
        }

        $account_id = $this->getFilter('account_id');
        if(is_numeric($account_id) && $account_id > -1) {
            $this->addWhere('FIND_IN_SET('.(int)$account_id.', account_id)');
        }

        $post_state = $this->getFilter('post_state');
        if(is_numeric($post_state) && $post_state > 0) {
            $this->addWhere('tweet.`post_state` = '.(int)$post_state);
        }

        $state = $this->getFilter('state');
        if(is_numeric($state) && $state > -1) {
            $this->addWhere('tweet.`published` = '.(int)$state);
        }

        $this->_search = array('message');
        $this->_orderby_default = 'post_date';
    }

    /**
     * Override buildQuery method
     *
     * @access protected
     * @param null
     * @return string
     */
    protected function buildQuery($query = '')
    {
        $query = "SELECT {tableAlias}.*, category.title AS category_name, category.url AS category_url, editor.name AS editor FROM {table} AS {tableAlias} ";
        $query .= " LEFT JOIN #__tweetscheduler_categories AS category ON {tableAlias}.category_id = category.id ";
        $query .= " LEFT JOIN #__users AS editor ON {tableAlias}.checked_out = editor.id ";
        return parent::buildQuery($query);
    }

    /**
     * Method to modify the data once it is loaded
     *
     * @access protected
     * @param array $data
     * @return array
     */
    protected function onDataLoad($data)
    {
        if(is_string($data->account_id)) {
            $data->account_id = explode(',', trim($data->account_id));
        }

        if(!empty($data->account_id)) {
            $accounts = $this->getAccounts();
            $data->accounts = array();
            foreach($accounts as $account) {
                if(in_array($account->id, $data->account_id)) {
                    $data->accounts[] = $account;
                }
            }
        }

        return $data;
    }

    /**
     * Method to fetch a list of all accounts
     *
     * @access protected
     * @param null
     * @return array
     */
    protected function getAccounts()
    {
        static $accounts;
        if(empty($accounts)) {
            $db = JFactory::getDBO();
            $query = 'SELECT * FROM #__tweetscheduler_accounts';
            $db->setQuery($query);
            $accounts = $db->loadObjectList();
        }
        return $accounts;
    }
}
