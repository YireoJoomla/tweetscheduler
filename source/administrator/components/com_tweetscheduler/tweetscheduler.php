<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
        
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the loader
require_once JPATH_COMPONENT.'/lib/loader.php';

// Load the libraries
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

// Make sure the user is authorised to view this page
$application = JFactory::getApplication();
$user = JFactory::getUser();

// Require the current controller
$view = JRequest::getCmd('view');
$controller_file = JPATH_COMPONENT.'/controllers/'.$view.'.php';
if(is_file($controller_file)) {
    require_once $controller_file; 
    $controller_name = 'TweetschedulerController'.ucfirst($view);
    $controller = new $controller_name();
} else {
    require_once 'controller.php';
    $controller = new TweetschedulerController();
}

// Perform the requested task
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

