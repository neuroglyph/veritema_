<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once JPATH_COMPONENT.'/controller.php';

// See if this is a request for a specific controller
$controller = JRequest::getCmd('controller');
if (!empty($controller) && file_exists(JPATH_COMPONENT.'/controllers/'.$controller.'.php'))
{
	require_once JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	$controller = 'RSMediaGalleryController'.$controller;
	$RSMediaGalleryController = new $controller();
}
else
	$RSMediaGalleryController = new RSMediaGalleryController();
	
$RSMediaGalleryController->execute(JRequest::getCmd('task'));

// Redirect if set
$RSMediaGalleryController->redirect();