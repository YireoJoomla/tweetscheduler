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
 * Tweetscheduler Controller
 */
class TweetschedulerController extends YireoController
{
    /**
     * Constructor
     * @package Tweetscheduler
     */
    public function __construct()
    {
        $this->_default_view = 'home';
        parent::__construct();
    }

    /**
     * Generic method to authorize the usage of a certain API
     *
     * @access public
     * @param null
     * @return null
     */
    public function redirectAuthorize()
    {
        // Set the view manually
        JRequest::setVar('view', 'account');

        // Fetch the model-data        
        $model = $this->_loadModel();
        $data = $model->getData();

        // If the consumer_key and/or consumer_secret are not valid, redirect to the form
        if (empty($data->consumer_key) || empty($data->consumer_secret)) {
            $this->msg = 'No consumer-key and consumer-secret configured';
            $this->msg_type = 'error';
            $this->doRedirect('accounts');
            return false;
        }

        // Switch for the various API-types
        switch($data->type) {
            case 'facebook':
                $rt = $this->authorizeFacebook($data);
                break;

            case 'linkedin':
                $rt = $this->authorizeLinkedin($data);
                break;
    
            default:
                $rt = $this->authorizeTwitter($data);
                break;
        }

        // Handle the response
        if($rt == false) {
            $this->msg_type = 'error';
            if(empty($this->msg)) $this->msg = 'Unable to authorize account';
        } else {
            $this->msg = 'Account is authorized';
        }

        $this->doRedirect('accounts');
        return $rt;
    }

    /**
     * Method to authorize the usage of Twitter API
     *
     * @access public
     * @param null
     * @return null
     */
    public function authorizeTwitter($data)
    {
        // Initialize the twitter-objects
        TweetschedulerHelper::initTwitterApi();

        // Initialize the twitter-client
        try {
            $twitter = new EpiTwitter($data->consumer_key, $data->consumer_secret);
        } catch(Exception $e) {
            $this->msg = $e->getMessage();
            return false;
        }

        // Set the callback
        /*try {
            $twitter->setCallback(JRoute::_('index.php?option=com_tweetscheduler&view=account&task=accountConfirm'));
        } catch(Exception $e) {
            $this->msg = $e->getMessage();
            return false;
        }*/

        // Get the request-token
        try {
            $token = $twitter->getRequestToken();
        } catch(Exception $e) {
            $this->msg = $e->getMessage();
            return false;
        }

        // Fetch the model-data        
        $model = $this->_loadModel();
        if(empty($model)) {
            $this->msg = 'Empty model';
            return false;
        }

        // Set this model-ID
        $model->setId($data->id);

        // Save the token in the model
        $data->oauth_token = $token->oauth_token;
        $data->oauth_token_secret = $token->oauth_token_secret;
        $model->store((array)$data);
        
        // Get the authorization URL
        try {
            $twitterUrl = $twitter->getAuthorizationUrl();
        } catch(Exception $e) {
            $this->msg = $e->getMessage();
            return false;
        }

        // Redirect to twitter authorization
        $this->setRedirect($twitterUrl);
        return true;
    }

    /**
     * Method to authorize the usage of Facebook API
     *
     * @access public
     * @param null
     * @return null
     */
    public function authorizeFacebook($data)
    {
        // Reset the OAuth credentials
        $model = $this->_loadModel();
        $data = $model->getData();
        $data->oauth_token = '';
        $data->oauth_token_secret = '';
        $model->store($data);

        // Initialize the Facebook-object
        $facebook = TweetschedulerHelper::getFacebook($data);

        // Fetch personal information
        $user = $facebook->getUser();
        if($user > 0) {
            try {
                $user_profile = $facebook->api('/me');
            } catch(Exception $e) {
                $user = null;
            }
        }

        // If login failed, redirect to the login
        if(empty($user)) {
            JFactory::getApplication()->redirect($facebook->getLoginUrl());
            JFactory::getApplication()->close();
        }

        // Fetch all permissions
        try {
            $permissions = $facebook->api("/me/permissions");
        } catch(Exception $e) {
            $this->msg = 'Unable to fetch permissions';
            return false;
        }

        // Check for the publish_stream permission
        if( array_key_exists('publish_stream', $permissions['data'][0]) == false ) {
            JFactory::getApplication()->redirect($facebook->getLoginUrl(array('scope' => 'publish_stream')));
            JFactory::getApplication()->close();
            return;
        }

        // Check for the publish_stream permission
        if( array_key_exists('manage_pages', $permissions['data'][0]) == false ) {
            JFactory::getApplication()->redirect($facebook->getLoginUrl(array('scope' => 'manage_pages')));
            JFactory::getApplication()->close();
            return;
        }

        // Fetch the model-data        
        $model = $this->_loadModel();
        if(empty($model)) {
            $this->msg = 'Empty model';
            return false;
        }

        // Set this model-ID
        $model->setId($data->id);

        // Set the access-token
        $data->oauth_token = $facebook->getAccessToken();
        $data->oauth_token_secret = $facebook->getAppSecret();

        // Save the token in the model
        if ($model->store((array)$data) == false) {
            $this->msg = $model->getError();
            return false;
        }
        
        $this->msg = 'Account is authorized';
        return true;
    }

    /**
     * Method to authorize the usage of Linkedin API
     *
     * @access public
     * @param null
     * @return null
     */
    public function authorizeLinkedin($data)
    {
        // Initialize the Linkedin-object
        $linkedin = TweetschedulerHelper::getLinkedin($data);
        return true;
    }

    /**
     * Method to confirm the usage of an API
     *
     * @access public
     * @param null
     * @return null
     */
    public function accountConfirm()
    {
        // Set the view manually
        JRequest::setVar('view', 'account');

        // Get the GET-data from Twitter
        $oauth_token = JRequest::getCmd('oauth_token');

        // If these details are empty, manual configuration is needed
        if(empty($oauth_token)) {
            $this->msg = 'Don\'t forget to manually configure the OAuth Token and the OAuth Token Secret';
            $this->doRedirect('accounts');
            return false;
        }

        // Load the saved data
        $db = JFactory::getDBO();
        $db->setQuery('SELECT * FROM #__tweetscheduler_accounts WHERE oauth_token='.$db->Quote($oauth_token));
        $data = $db->loadObject();
        if (empty($data)) {
            $this->msg = 'No data found for OAuth-token "'.$oauth_token.'"';
            $this->msg_type = 'error';
            $this->doRedirect('accounts');
            return false;
        }

        // Fetch the model-data        
        $model = $this->_loadModel();
        if(empty($model)) {
            $this->msg = 'Empty model';
            $this->msg_type = 'error';
            $this->doRedirect('accounts');
            return false;
        }

        // Set this model-ID
        $model->setId($data->id);

        // Get the twitter object
        $twitter = TweetschedulerHelper::getTwitter($data);

        // Handle 
        $twitter->setToken($oauth_token);
        $token = $twitter->getAccessToken();

        // Add the new data to the model-data
        $data->oauth_token_secret = $token->oauth_token_secret;
        $data->oauth_token = $token->oauth_token;

        // Insert the new access-token
        $twitter->setToken($token->oauth_token, $token->oauth_token_secret);

        // Save the token in the model
        if ($model->store((array)$data) == false) {
            $this->msg = $model->getError();
            $this->msg_type = 'error';
            $this->doRedirect('accounts');
            return false;
        }
        
        // Fetch the screen_name
        $twitterInfo = $twitter->get_accountVerify_credentials();
        $twitterInfo->response;
        $twitter_account = $twitterInfo->screen_name;

        // Give feedback to the Joomla! application
        $this->msg = JText::sprintf('Twitter-account "%s" is authorised', $twitter_account);
        $this->doRedirect('accounts');
    }

    /**
     * Method to test the usage of an API
     *
     * @access public
     * @param null
     * @return null
     */
    public function test()
    {
        // Fetch the model-data        
        $model = $this->_loadModel();
        if(empty($model)) {
            $this->msg = 'Empty model';
            $this->msg_type = 'error';
            $this->doRedirect('accounts');
            return false;
        }

        // Fetch the data
        $data = $model->getData();
        if(empty($data)) {
            $this->msg = 'Empty data';
            $this->msg_type = 'error';
            $this->doRedirect('accounts');
            return false;
        }

        // Switch for the various API-types
        switch($data->type) {
            case 'facebook':
                $rt = $this->testFacebook($data);
                break;

            case 'linkedin':
                $rt = $this->testLinkedin($data);
                break;
    
            default:
                $rt = $this->testTwitter($data);
                break;
        }

        if($rt == false) $this->msg_type = 'error';
        return $this->doRedirect('accounts');
    }

    /**
     * Method to test the usage of the Twitter-API
     *
     * @access public
     * @param null
     * @return null
     */
    public function testTwitter($data)
    {
        // Get the twitter object
        $twitter = TweetschedulerHelper::getTwitter($data);

        // Handle the twitter-call
        try {
            //$twitterInfo = $twitter->get_accountVerify_credentials();
            $twitterInfo = $twitter->get('/account/verify_credentials.json');
        } catch(Exception $e) {
            $response = $e->getMessage();
            if(substr($response, 0, 2) == '{"') {
                $response = json_decode($response);
                if(isset($response->error)) $error = (string)$response->error;
                if(isset($response->errors[0]->message)) $error = (string)$response->errors[0]->message;
            } else {
                $error = $response;
            }
            $this->msg = 'API error: '.$error;
            return false;
        }

        // Fetch the response
        $response = $twitterInfo->response;
        $twitter_account = $twitterInfo->screen_name;

        // Give feedback to the Joomla! application
        if (empty($twitter_account)) {
            $this->msg = JText::_('Twitter authentication failed');
            return false;
        } else {
            $this->msg = JText::sprintf('Twitter-account is set to "%s"', $twitter_account);
        }
            
        return true;
    }

    /**
     * Method to test the usage of the Facebook-API
     *
     * @access public
     * @param null
     * @return null
     */
    public function testFacebook($data)
    {
        // Get the object
        $facebook = TweetschedulerHelper::getFacebook($data);

        // Fetch personal information
        $user = $facebook->getUser();

        // Give feedback to the Joomla! application
        if (empty($user)) {
            $this->msg = JText::_('Facebook authentication failed');
            return false;
        } else {
            $user = $facebook->api('/me');
            $this->msg = JText::sprintf('Facebook-account is set to "%s"', $user['username']);
        }
            
        return true;
    }

    /**
     * Method to test the usage of the Linkedin-API
     *
     * @access public
     * @param null
     * @return null
     */
    public function testLinkedin($data)
    {
        // Get the object
        $linkedin = TweetschedulerHelper::getLinkedin($data);

        // Fetch personal information
        $url = 'http://api.linkedin.com/v1/people/~?format=json';
        $response = $linkedin->fetch('GET', $url);
    
        // Give feedback to the Joomla! application
        if (empty($response['linkedin'])) {
            $this->msg = JText::_('Linkedin test failed');
            return false;
        } else {
            $data = json_decode($response['linkedin'], true);
            if ($data['status'] == 401) {
                $this->msg = JText::_('Linkedin test failed: '.$data['message']);
                return false;
            } else {
                $user = $data['firstName'].' '.$data['lastName'];
                $this->msg = JText::sprintf('Linkedin-account is set to "%s"', $user);
            }
        }

        return true;
    }

    /**
     * Method to test the usage of an API
     *
     * @access public
     * @param null
     * @return null
     */
    public function send()
    {
        // Fetch the model-data        
        $model = $this->_loadModel();
        $data = $model->getData();

        // Post the tweet
        $response = TweetschedulerHelper::post($data);

        // Give feedback to the Joomla! application
        if (empty($response)) {
            $this->msg = JText::_('Update failed');
            $this->msg_type = 'error';

        } else if (!empty($response['error'])) {
            $this->msg = JText::sprintf('Update failed: %s', $response['error']);
            $this->msg_type = 'error';

        } elseif(!empty($response['errors'][0]['message'])) {
            $this->msg = JText::sprintf('Update failed: %s', $response['errors'][0]['message']);
            $this->msg_type = 'error';

        } else {
            $this->msg = JText::_('Update was successful');
        }

        $this->doRedirect('tweets');
    }

    /**
     * Method to run SQL-update queries
     *
     * @access public 
     * @param null
     * @return null
     */
    public function updateQueries()
    {
        // Run the update-queries
        require_once JPATH_COMPONENT.'/helpers/update.php';
        TweetschedulerUpdate::runUpdateQueries();

        // Redirect
        $link = 'index.php?option=com_tweetscheduler&view=home';
        $msg = JText::_('Applied database upgrades');
        $this->setRedirect($link, $msg);
    }

    /**
     * Method to delete all posted tweets
     *
     * @access public 
     * @param null
     * @return null
     */
    public function deletePosted()
    {
        // Run the update-queries
        $db = JFactory::getDBO();
        $db->setQuery('DELETE FROM #__tweetscheduler_tweets WHERE `post_state`=1');
        $db->query();

        // Redirect
        $link = 'index.php?option=com_tweetscheduler&view=tweets';
        $msg = JText::_('Cleaned all posted tweets');
        $this->setRedirect($link, $msg);
    }

    /**
     * Method to autospread posts
     *
     * @access public 
     * @param null
     * @return null
     */
    public function autospread()
    {
        // Security check
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the ID-list
        $cid = $this->getIds();
        if (count( $cid ) < 1) {
            JError::raiseError(500, JText::_('LIB_YIREO_CONTROLLER_ITEM_SELECT_PUBLISH'));
        }

        // Fetch the model-data        
        $model = $this->_loadModel();
        $model->autospread($cid);

        $link = 'index.php?option=com_tweetscheduler&view=tweets';
        $msg = JText::_('Automatically spreaded selected tweets');
        $this->setRedirect($link, $msg);
    }

    /**
     * Method to test the usage of an API
     *
     * @access public
     * @param null
     * @return null
     */
    public function post_date()
    {
        // Get variables
        $application = JFactory::getApplication();
        $input = $application->input;

        // Get input
        $tweet_id = $input->getInt('id');
        $post_date = $input->getString('post_date');

        // Convert date to proper timezone
        $timezone = TweetschedulerHelper::getTimezone();
        $post_date = new JDate($post_date, $timezone);
        $post_date = $post_date->format('Y-m-d H:i:s', false, false);

        // Modify the tweet in the database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__tweetscheduler_tweets'))
            ->set($db->quoteName('post_date').'='.$db->quote($post_date))
            ->set($db->quoteName('utc').'=1')
            ->where($db->quoteName('id').'='.$tweet_id);
        $db->setQuery($query);
        $db->query();

        // Output
        $timezone = TweetschedulerHelper::getTimezone();
        $post_date = new JDate($post_date);
        $post_date->setTimezone($timezone);
        $post_date = $post_date->format('Y-m-d H:i', $timezone);
        $post_date_output = TweetschedulerHelper::formatDatetime($post_date);
        $post_date_output .= ' ('.TweetschedulerHelper::getRelativeTime($post_date).')';
        echo json_encode(array('post_date' => $post_date_output));

        $application->close();
        exit;
    }
}
