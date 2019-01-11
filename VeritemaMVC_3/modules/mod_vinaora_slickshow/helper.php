<?php
/**
 * @version		$Id: helper.php 2012-03-19 vinaora $
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

require_once dirname(__FILE__).DS."helper".DS."vinaora_param.php";

class modVinaoraSlickshowHelper
{
	/*
	 * Valid Params
	 */
	public static function &validParams(&$params){

		// Check Module Cass Suffix
		$param = htmlspecialchars( trim($params->get( 'moduleclass_sfx' )) );
		$params->set('moduleclass_sfx', $param);

		// Check Width Stage
		$param = intval($params->get('width'));
		$param = abs($param);
		$params->set('width', $param);

		// Check Height Stage
		$param = intval($params->get('height'));
		$param = abs($param);
		$params->set('height', $param);

		// Check StartWith
		$param = intval($params->get('startWith'));
		$param = abs($param);
		$params->set('startWith', $param);

		// Check Background Color
		$param = $params->get('backgroundColor', '#ffffff');
		$param = modVinaoraSlickshowParam::validColor($param, '', '#');
		$params->set('backgroundColor', $param);

		// Check Background Transparency
		$param = $params->get('backgroundTransparency', 0);
		$param = modVinaoraSlickshowParam::validTransparency($param);
		$params->set('backgroundTransparency', $param);

		// Check Navigation Bar Height
		$param = intval($params->get('barHeight', 35));
		$param = abs($param);
		$params->set('barHeight', $param);

		// Check Navigation Bar Color
		$param = $params->get('barColor', '#ffffff');
		$param = modVinaoraSlickshowParam::validColor($param, '', '#');
		$params->set('barColor', $param);

		// Check Navigation Bar Transparency
		$param = $params->get('barTransparency', 5);
		$param = modVinaoraSlickshowParam::validTransparency($param);
		$params->set('barTransparency', $param);

		// Check FadeTransition
		$param = $params->get('fadeTransition');
		$param = $param ? 'true' : 'false';
		$params->set('fadeTransition', $param);

		// Check VerticalTransition
		$param = $params->get('verticalTransition');
		$param = $param ? 'true' : 'false';
		$params->set('verticalTransition', $param);

		// Check ControllerTop
		$param = $params->get('controllerTop');
		$param = $param ? 'true' : 'false';
		$params->set('controllerTop', $param);

		// Check Slideshow Time
		$param = $params->get('slideShowTime');
		$param = abs(intval($param));
		$params->set('slideShowTime', $param);

		return $params;
	}

	/*
	 * Get content of the config file
	 */
	public static function getConfig($name){

		$xml	= NULL;
		$name	= JPath::clean($name);

		//Remove the Directory Separator (DS) at the begin of $name if exits
		$name = ltrim($name, DS);


		if ( !is_file(JPATH_BASE.DS.$name) ){
			// JFactory::getApplication()->enqueueMessage( JText::_( 'Some error occurred' ), 'error' );
			// JError::raiseWarning( 100, JText::sprintf( 'MOD_VINAORA_SLICKSHOW_ERROR_FILE_NOT_FOUND_OR_INVALID', $name ) );
			return NULL;
		}

		// Load from file if it is .xml
		// $ext = pathinfo($filename, PATHINFO_EXTENSION);
		// strtolower(substr($name, -4, 4)) == '.xml'
		if ( preg_match('/[^\s]+(\.(?i)(xml))$/', $name) ){

			$xml = simplexml_load_file( JPATH_BASE.DS.$name );
		}
		// Load from url if it is .xml.php
		// strtolower(substr($name, -8, 8)) == '.xml.php'
		elseif ( preg_match('/[^\s]+(\.(?i)(xml)\.(?i)(php))$/', $name) ){
			$xml = simplexml_load_file( JURI::base().JPath::clean($name, '/') );
		}
		else{
			// JError::raiseWarning( 100, JText::sprintf( 'MOD_VINAORA_SLICKSHOW_ERROR_FILE_NOT_VALID_XML_FORMAT', $name ) );
			return NULL;
		}

		return $xml;

	}

	/*
	 * Create the config file
	 */
	public static function createConfig($name, $params){

		jimport('joomla.filesystem.file');
		$name = JPath::clean($name);

		//Remove the Directory Separator (DS) at the begin of $name if exits
		$name = ltrim($name, DS);

		$xml = self::_createXML($params);
		if(strpos($xml, "<items>") === false){
			echo JText::_('MOD_VINAORA_SLICKSHOW_ERROR_IMAGE_NOT_FOUND');
			return false;
		}

		if ( is_writeable(dirname(JPATH_BASE.DS.$name)) ){
			if ( JFile::write(JPATH_BASE.DS.$name, $xml) ) return true;
			else{
				// Write file error
				JError::raiseWarning( 100, JText::sprintf( 'MOD_VINAORA_SLICKSHOW_ERROR_WRITE_FILE_ERROR', $name ) );
			}
		}
		else{
			// Directory is not writeable
			$preg = ( DS == '\\' ) ? '/\\\\[^\\\\]+$/' : '/\/[^\/]+$/';
			JError::raiseWarning( 100, JText::sprintf( 'MOD_VINAORA_SLICKSHOW_ERROR_DIRECTORY_IS_NOT_WRITABLE', preg_replace($preg, '', $name) ));
		}

		return false;

	}

	/*
	 * Create content of the config file
	 */
	private static function _createXML($params){
		$xml = '<?xml version="1.0" encoding="utf-8"?>';

		// Create Element - <banner>
		$node = new SimpleXMLElement($xml.'<banner />');

		// Create General Settings
		$node = &self::_createGeneral($node, $params);

		// Create Items
		$node = &self::_createItems($node, $params);

		$xml = $node->asXML();
		// Not need if use SimpleXMLElement
		// $xml = str_replace('slideshowtime>', 'slideShowTime>', $xml);

		// Remove Empty Elements
		$xml = preg_replace('/<[\w-]+><\/[\w-]+>/', '', $xml);
		$xml = preg_replace('/<[\w-]+\/>/', '', $xml);

		return $xml;
	}

	/*
	 * Create General Settings
	 */
	private static function &_createGeneral(&$node, $params){

		$attbs = "width|height|backgroundColor|backgroundTransparency|startWith|barHeight|fadeTransition|verticalTransition|controllerTop|transitionSpeed|titleX|titleY";
		$attbs = explode("|", $attbs);
		foreach ($attbs as $attb){
			$node->addAttribute($attb, $params->get($attb));
		}

		return $node;
	}

	/*
	 * Create Items
	 */
	private static function &_createItems(&$node, $params){
		$items	= self::_getItems($params);
		if (!empty($items) && count($items)){
			// Create Element - <banner>.<items>
			$nodeL1 = &$node->addChild('items');
			foreach ($items as $key=>$path){
				if(!empty($path)){
					$nodeL1 = &self::_createItem($nodeL1, $params, $path, $key+1);
				}
			}
		}

		return $node;
	}

	/*
	 * Create a Item Element
	 */
	private static function &_createItem(&$node, $params, $path, $position=1){
		// Create Element - <banner>.<items>.<item>
		$nodeL1 = &$node->addChild('item');

		// Create Element - <banner>.<items>.<item>.<title>
		$param	= $params->get('item_title');
		$param	= str_replace('|', "\r\n", $param);
		$param	= modVinaoraSlickshowParam::getParam($param, $position, "\n");
		$param	= trim($param);
		// $param	= '<![CDATA[' .$param . ']]>';
		$nodeL2	= &$nodeL1->addChild('title', $param);

		// Create Element - <banner>.<items>.<item>.<path>
		$param	= $path;
		$nodeL2	= &$nodeL1->addChild('path', $param);

		// Create Element - <banner>.<items>.<item>.<url>
		$param	= $params->get('item_url');
		$param	= str_replace("|", "\r\n", $param);
		$param	= modVinaoraSlickshowParam::getParam($param, $position, "\n");
		$param	= trim($param);
		$nodeL2	= &$nodeL1->addChild('url', $param);

		// Create Element - <banner>.<items>.<item>.<target>
		$param	= $params->get('item_target');
		$param	= str_replace("|", "\r\n", $param);
		$param	= modVinaoraSlickshowParam::getParam($param, $position, "\n");
		$param	= trim($param);
		$param	= modVinaoraSlickshowParam::validTarget($param);
		$nodeL2	= &$nodeL1->addChild('target', $param);

		// Create Element - <banner>.<items>.<item>.<bar_color>
		$param	= $params->get('item_bar_color');
		$param	= str_replace("|", "\r\n", $param);
		$param	= modVinaoraSlickshowParam::getParam($param, $position, "\n");
		$param	= trim($param);
		if (empty($param)){
			$param	= $params->get('barColor');
		}
		else{
			$param	= modVinaoraSlickshowParam::validColor($param, substr($params->get("barColor"), -6, 6), "0x");
		}

		$nodeL2	= &$nodeL1->addChild('bar_color', $param);

		// Create Element - <banner>.<items>.<item>.<bar_transparency>
		$param	= $params->get('item_bar_transparency');
		$param	= str_replace("|", "\r\n", $param);
		$param	= modVinaoraSlickshowParam::getParam($param, $position, "\n");
		$param	= trim($param);
		if (empty($param)){
			$param = $params->get("barTransparency");
		}
		else{
			$param	= modVinaoraSlickshowParam::validTransparency($param);
		}

		$nodeL2	= &$nodeL1->addChild('bar_transparency', $param);

		// Create Element - <banner>.<items>.<item>.<slideShowTime>
		$param	= $params->get('item_slideShowTime');
		$param	= str_replace("|", "\r\n", $param);
		$param	= modVinaoraSlickshowParam::getParam($param, $position, "\n");
		$param	= trim($param);
		if ($param == ""){
			$param = $params->get("slideShowTime");
		}
		else{
			$param	= abs(intval($param));
		}
		$nodeL2	= &$nodeL1->addChild('slideShowTime', $param);

		return $node;
	}

	/*
	 * Get List of Items from a Folder
	 */
	private static function _getItems($params){
		// return $items;
		$param	= $params->get('item_path');
		$param	= str_replace(array("\r\n","\r"), "\n", $param);
		$param	= explode("\n", $param);

		// Get Paths from invidual paths
		foreach($param as $key=>$value){
			$param[$key] = self::validPath($value);
		}

		// Remove empty element
		$param = array_filter($param);

		// Get Paths from directory
		if (empty($param)){
			$param	= $params->get('item_dir');

			if ($param == "-1") return null;

			$filter		= '([^\s]+(\.(?i)(jpg|png|gif|bmp))$)';
			$exclude	= array('index.html', '.svn', 'CVS', '.DS_Store', '__MACOSX', '.htaccess');
			$param	= JFolder::files(JPATH_BASE.DS.'images'.DS.$param, $filter, true, true, $exclude);
			foreach($param as $key=>$value){
				$value = substr($value, strlen(JPATH_BASE.DS) - strlen($value));
				$param[$key] = self::validPath($value);
			}
		}

		// Reset keys
		$param = array_values($param);
		return $param;

	}

	/*
	 * Get the Valid Path of Item
	 */
	public static function validPath($path){
		$path = trim($path);

		// Check file type is image or not
		if( !preg_match('/[^\s]+(\.(?i)(jpg|png|gif|bmp))$/', $path) ) return '';

		// The path includes http(s)
		if( preg_match('/^(?i)(https?):\/\//', $path) ){
			$base = JURI::base(false);
			if (substr($path, 0, strlen($base)) == $base){
				$path = substr($path, strlen($base) - strlen($path));
			}
			else return $path;
		}

		// The path not includes http(s)
		$path = JPath::clean($path, DS);
		$path = ltrim($path, DS);

		if (!is_file($path)){
			if (!is_file(dirname(JPATH_BASE).DS.$path)) return '';
			else{
				$path = JPath::clean("/".$path, "/");
			}
		}
		else{
			// Convert it to url path
			$path = JPath::clean(JURI::base(true)."/".$path, "/");
		}

		return $path;
	}

	/*
	 * Add SWFObject Library to <head> tag
	 */
	public static function addSWFObject($source='local', $version='2.2'){

		switch($source){

			case 'local':
				JHtml::script("media/mod_vinaora_slickshow/js/swfobject/$version/swfobject.js");
				break;

			case 'google':
				JHtml::script("http://ajax.googleapis.com/ajax/libs/swfobject/$version/swfobject.js");
				break;

			default:
				return false;
		}
		return true;

	}
}
