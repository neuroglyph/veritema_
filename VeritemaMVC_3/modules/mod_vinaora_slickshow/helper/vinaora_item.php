<?php

/**

 * @version		$Id: vinaora_item.php 2011-07-05 vinaora $

 * @package		VINAORA SLICK SLIDESHOW

 * @subpackage	mod_vinaora_slickshow

 * @copyright	Copyright (C) 2010 - 2011 VINAORA. All rights reserved.

 * @license		GNU General Public License version 2 or later; see LICENSE.txt

 * @website		http://vinaora.com

 * @twitter		http://twitter.com/vinaora

 * @facebook	http://facebook.com/vinaora

 */



// no direct access

defined('_JEXEC') or die;



class modVinaoraSlickshowItem

{



	/*

	 * Get List of Items from a Folder

	 */

	public static function getItems($folder, $root, $default, $filter, $exclude, $fullpath=false){

	

		jimport('joomla.filesystem.folder');

		$items = NULL;

	

		switch($folder){

			// None selected folder

			case '-1':

				break;



			// Choose default folder

			case '0':

			case '':

				if ( JFolder::exists($default) ){

					$items =  JFolder::files($default, $filter, false, $fullpath, $exclude);

				}

				break;



			// Choose a subfolder of [Joomla]/images/

			default:

				if ( JFolder::exists($root.DS.$folder) ){

					$items =  JFolder::files($root.DS.$folder, $filter, false, $fullpath, $exclude);

				}

				break;

		}



		return $items;



	}



	/*

	 * Get Item Path

	 */

	public static function getItemPath($folder, $root='images/', $default=''){



		$path = NULL;



		switch($folder){

			// None selected folder

			case '-1':

				break;

			

			// Choose default folder

			case '0':

			case '':

				if (!empty($default)) $path = JURI::base(true).'/'.$default;

				break;

			

			// Choose a subfolder of [Joomla]/images/

			default:

				$path = JURI::base(true).'/'.$root.$folder;

				break;

		}

		

		return $path;

		

	}



}

