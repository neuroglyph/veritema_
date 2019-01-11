<?php
/**
 * @version		$Id: mod_vinaora_slickshow.php 2012-03-19 vinaora $
 * @package		VINAORA SLICK SLIDESHOW
 * @subpackage	mod_vinaora_slickshow
 * @copyright	Copyright (C) 2010 - 2012 VINAORA. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @website		http://vinaora.com
 * @twitter		http://twitter.com/vinaora
 * @facebook	http://facebook.com/vinaora
 */

// no direct access
defined('_JEXEC') or die;

// Require Base Helper
require_once dirname(__FILE__).DS.'helper.php';

// Valid the parameters
$params = &modVinaoraSlickshowHelper::validParams($params);

$module_id			= $module->id;

$config_custom		= $params->get( 'config_custom' );
$lastedit			= $params->get( 'config_code' );
$config_name		= "V$module_id-$lastedit.xml";

if( $config_custom=="-1" ){
	$config_name = 'media/mod_vinaora_slickshow/config/'.$config_name;
}
else{
	$config_name = 'media/mod_vinaora_slickshow/config/custom/'.$config_custom;
}

$config = modVinaoraSlickshowHelper::getConfig($config_name);

// Config file (.xml or .xml.php) exits and valid XML
if ( $config ){

	// Get the size of stage flash
	$param	= $config->attributes()->width;
	$params->set('width', $param);

	$param	= $config->attributes()->height;
	$params->set('height', $param);

	$param	= $config->attributes()->backgroundColor;
	$params->set('backgroundColor', $param);
}
// Config File not exist
else{
	// Background for config file .xml
	$backgroundColor = $params->get( 'backgroundColor' );
	$backgroundColor = preg_replace("/^#/", "0x", $backgroundColor);
	$params->set( 'backgroundColor', $backgroundColor );
	if($config_custom=="-1") modVinaoraSlickshowHelper::createConfig($config_name, $params);
}

// Add SWFObject Library to <head> tag
modVinaoraSlickshowHelper::addSWFObject( $params->get('swfobject_source'), $params->get('swfobject_version') );

// Initialize variables
$media					= modVinaoraSlickshowParam::getPath('media/mod_vinaora_slickshow/');
$config_path 			= modVinaoraSlickshowParam::getPath($config_name);
$stage_path 			= $media.'flash/vinaora_slickshow.swf';
$expressInstall_path 	= $media.'js/swfobject/expressInstall.swf';
$flash_version			= '9.0.124';

// Get flash params
$flash_wmode	= $params->get( 'flash_wmode' );

$width					= $params->get( 'width' );
$height					= $params->get( 'height' );

// Background for script
$backgroundColor		= $params->get( 'backgroundColor' );
$backgroundColor		= preg_replace("/^0x/", "#", $backgroundColor);

$container				= 'vinaora-slick-slideshow'.$module_id;

// Get border parameters
$border_width			= intval($params->get('border_width', 0));
$border_color			= $params->get('border_color', '#ffffff');
$border_style			= $params->get('border_style', 'solid');
$border_rounded			= $params->get('border_rounded', 1);
$border_shadow			= $params->get('border_shadow', 1);

$moduleclass_sfx		= $params->get( 'moduleclass_sfx' );

// Load Default Layout
require JModuleHelper::getLayoutPath('mod_vinaora_slickshow', $params->get('layout', 'default'));
