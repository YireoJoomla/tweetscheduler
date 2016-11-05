<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author    Yireo (info@yireo.com)
 * @copyright Copyright 2016
 * @license   GNU Public License
 * @link      https://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load the Yireo library
jimport('yireo.loader');

// Check for helper
if (!class_exists('YireoHelperInstall'))
{
	require_once JPATH_COMPONENT . '/helpers/yireo/install.php';
	YireoHelperInstall::getInstance()->autoInstallLibrary('yireo', 'https://www.yireo.com/documents/lib_yireo_j3x.zip', 'Yireo Library');
	$application = JFactory::getApplication();
	$application->redirect('index.php?option=com_installer');
	$application->close();
}

// Check for function
if (!class_exists('\Yireo\System\Autoloader'))
{
	die('Yireo Library is not installed and could not be installed automatically');
}

// Load the libraries
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php';

// Make sure the user is authorised to view this page
$input = JFactory::getApplication()->input;
$user  = JFactory::getUser();

// Require the current controller
$view           = $input->getCmd('view');
$controllerFile = JPATH_COMPONENT . '/controllers/' . $view . '.php';

if (is_file($controllerFile))
{
	require_once $controllerFile;
	$controllerName = 'TweetschedulerController' . ucfirst($view);
	$controller     = new $controllerName;
}
else
{
	require_once 'controller.php';
	$controller = new TweetschedulerController;
}

// Perform the requested task
//echo $input->getCmd('task');exit;
$controller->execute($input->getCmd('task'));
$controller->redirect();

