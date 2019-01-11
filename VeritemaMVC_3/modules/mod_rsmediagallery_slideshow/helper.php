<?php
/**
* @version 1.0.0
* @package RSMediaGallery! 1.0.0
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

class modRSMediaGallerySlideshowHelper
{
	public static function _addStyleSheet($file)
	{
		$document = JFactory::getDocument();
		static $template;
		
		if (empty($template))
		{
			$app	  = JFactory::getApplication();
			$template = $app->getTemplate();
		}

		if (file_exists(JPATH_SITE.'/templates/'.$template.'/html/mod_rsmediagallery_slideshow/assets/css/'.$file))
			$document->addStyleSheet(JURI::root(true).'/templates/'.$template.'/html/mod_rsmediagallery_slideshow/assets/css/'.$file);
		else
			$document->addStyleSheet(JURI::root(true).'/modules/mod_rsmediagallery_slideshow/assets/css/'.$file);
	}
}