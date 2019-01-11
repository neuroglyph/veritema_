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



jimport('joomla.filesystem.file');



abstract class modVsir

{



	public static function getImages(&$params, $folder)

	{

		$images	= array();

		

		$links    = $params->get( 'links' );

		$alts     = $params->get( 'alts' );

		$titles   = $params->get( 'titles' );

		

		if ($links)

			$links  = preg_split("/[\n\r]+/", $links);

		if ($alts)

			$alts   = preg_split("/[\n\r]+/", $alts);

		if ($titles)

			$titles = preg_split("/[\n\r]+/", $titles);

			

		$target   = $params->get( 'target', '_self' );

		$repeat   = $params->get( 'repeat', 0 );

		$substoo  = $params->get( 'substoo', 0 );

		$random   = $params->get( 'random', 0 );

		$width    = (int)$params->get( 'width' );

		$height   = (int)$params->get( 'height' );



		// check if directory exists

		if (is_dir(JPATH_BASE.DS.$folder))

		{

			$files = modVsir::getFiles($folder,$substoo);

			$i = 0;

			$repeated = 0;

			foreach ($files as $file)

			{

				if (modVsir::isImage($file)) {

					$file	    = str_replace( '\\', '/', $file );

					$alt        = '';

					$attribs    = array();

					$attribs['width'] = intval($width);

					$attribs['height'] = intval($height);

					if ($alts && isset($alts[$i]))

						$alt = $alts[$i];

					if ($titles && isset($titles[$i]))

						$attribs['title'] = $titles[$i];

					

					$images[$i] = JHTML::_('image', $file, $alt, $attribs);

					if ($links && (isset($links[$i]) or $repeat)) :

						if ($links[$i]) {

							$images[$i] = JHTML::_('link', trim($links[$i]), $images[$i], ($target ? array('target' => '_blank') : '' ) );

						} else {

							$repeated++;

							$images[$i] = JHTML::_('link', trim($links[$repeated-1]), $images[$i], ($target ? array('target' => '_blank') : '' ) );

							if ($repeated == count($links)) $repeated = 0 ;

						}

					endif;

					++$i;

				}

			}

		}

		

		if ($random) :

			shuffle($images);

		endif;



		return $images;

	}



	public static function getFolder(&$params)

	{

		$folder 	= $params->get( 'folder' );

		

		// Remove the trailing slash from the url (if any)

		if (substr($folder, -1) == '/') {

			$folder = substr($folder, 0 ,-1);

		}



		$LiveSite 	= JURI::base();



		// if folder includes livesite info, remove

		if ( JString::strpos($folder, $LiveSite) === 0 ) {

			$folder = str_replace( $LiveSite, '', $folder );

		}

		// if folder includes absolute path, remove

		if ( JString::strpos($folder, JPATH_SITE) === 0 ) {

			$folder= str_replace( JPATH_BASE, '', $folder );

		}

		$folder = str_replace('\\',DS,$folder);

		$folder = str_replace('/',DS,$folder);



		return $folder;

	}

	

	public static function getFiles($folder,$substoo) {

	

		$dir      = JPATH_BASE.DS.$folder;

			

		$files	  = array();

		$subfiles = array();

	

		if ($handle = opendir($dir)) {

			while (false !== ($file = readdir($handle))) {

				if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html' ) {

					if (!is_dir($dir . DS . $file))

					{

						$files[] = $folder . DS . $file;

					} elseif ($substoo != 0) {

						$newfolder = $folder . DS . $file;

						$subfiles[]  = modVsir::getFiles($newfolder,$substoo,$random);

					}

				}

			}

		}

		closedir($handle);

		

		sort($files);

		

		foreach ($subfiles as $subfile) :

			$files = array_merge($files,$subfile);

		endforeach;

		

		return $files;

	}

		

    public static function isImage($file) {

		$file  = JFile::getName($file);

		$file  = JFile::getExt($file);

		$file  = strtolower($file);

        $types = array("jpg", "jpeg", "gif", "png");

        if (in_array($file, $types)) return true;

        else return false;

    }



	public static function buildCSS ( &$params,$mod_vsig_id ) {



		$width    = $params->get('width', 'auto');

		$heigh    = $params->get( 'height', 'auto');

		

		$style = "#vsir_" . $mod_vsig_id . " {"

			. " position:relative;"

			. " height:".$heigh."px;"

			. " width:".$width."px;"

			. " }\n"

			. "#vsir_" . $mod_vsig_id . " .vsig_slide_" . $mod_vsig_id . " {"

			. " height:".$heigh."px;"

			. " position:absolute;"

			. " width:".$width."px;"

			. " }";



		return $style;

	}

}

