<?php

/*------------------------------------------------------------------------

# mod_vsir - Very Simple Image Rotator

# ------------------------------------------------------------------------

# author    Joomla!Vargas

# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://joomla.vargas.co.cr

# Technical Support:  Forum - http://joomla.vargas.co.cr/forum

-------------------------------------------------------------------------*/



// no direct access

defined('_JEXEC') or die;



global $mod_vsig_id;



if ( !$mod_vsig_id ) : $mod_vsig_id = 1; endif;



// Include the syndicate functions only once

require_once (dirname(__FILE__).DS.'helper.php');



$folder	= modVsir::getFolder($params);

$images	= modVsir::getImages($params, $folder);



if (!count($images)) {

	echo JText::_( 'No images ');

	return;

}



require JModuleHelper::getLayoutPath('mod_vsir', $params->get('layout', 'default'));



$mod_vsig_id++;

