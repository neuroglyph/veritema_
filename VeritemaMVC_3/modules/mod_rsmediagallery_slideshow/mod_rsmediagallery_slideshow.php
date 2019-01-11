<?php
/**
* @version 1.0.0
* @package RSMediaGallery! 1.0.0
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php') && file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/jquery.php'))
{
	jimport('joomla.application.component.helper');
	
	require_once dirname(__FILE__).'/helper.php';
	require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
	require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/jquery.php';
	
	$componentParams = JComponentHelper::getParams('com_rsmediagallery');
	$jqueryHelper 	 = RSMediaGalleryjQuery::getInstance();
	$document		 = JFactory::getDocument();

	// CSS
	// handle template overrides
	modRSMediaGallerySlideshowHelper::_addStyleSheet('style.css');
	modRSMediaGallerySlideshowHelper::_addStyleSheet($componentParams->get('contrast', 'light').'.css');
	
	$src = JDEBUG ? '.src' : '';
	
	// JS
	$jqueryHelper->addjQuery();
	$document->addScriptDeclaration("jQuery.noConflict();");
	$document->addScript(JURI::root(true).'/modules/mod_rsmediagallery_slideshow/assets/js/jquery.bxslider'.$src.'.js');
	$document->addScript(JURI::root(true).'/modules/mod_rsmediagallery_slideshow/assets/js/jquery.easing'.$src.'.js');

	$tags 					= $params->get('tags');
	$order 					= $params->get('ordering', 'ordering');
	$direction 				= strtoupper($params->get('direction', 'asc'));
	$limit					= $params->get('limit');
	$use_url 				= $params->get('use_url', 1);
	$show_title 			= $params->get('show_title', 1);
	$show_description 		= $params->get('show_description', 1);
	$show_controls 			= $params->get('show_controls', 1);
	$controls 				= $show_controls ? 'true' : 'false';
	$show_pager				= $params->get('show_pager', 1);
	$pager 					= $show_pager ? 'true' : 'false';
	$pager_position 		= $params->get('pager_position', 'bottom-outside');
	$pager_text_position	= $params->get('pager_text_position', 'center');
	$pagerType				= $params->get('pager_type', 'full');
	$speed 					= (int) $params->get('speed', 1500);
	$pause 					= (int) $params->get('pause', 3000) + $speed;
	$randomStart			= $params->get('random_slide', 0) ? 'true' : 'false';
	$auto					= $params->get('auto', 1) ? 'true' : 'false';
	$autoHover				= $params->get('auto_hover', 1) ? 'true' : 'false';
	$easing					= $params->get('easing', 'swing');
	$infiniteLoop			= $params->get('infinite_loop', 1) ? 'true' : 'false';
	$use_original			= $params->get('use_original', 0);
	$image_position			= $params->get('image_position', 'left');
	$image_borders			= $params->get('image_borders', 1);
	$mode					= $params->get('mode', 'horizontal');
	$use_fixed_width		= $params->get('use_fixed_width', 0);
	$fixed_width			= (int) $params->get('width', 700);
	$open_in_new_page		= $params->get('open_in_new_page', 0);

	$params					= RSMediaGalleryHelper::parseParams($params);
	$width					= $params->get('thumb_width');
	$height					= $params->get('thumb_height');	
	$max_height				= 0;

	$items = RSMediaGalleryHelper::getItems($tags, $order, $direction, 0, $limit);

	if ($items)
		foreach ($items as $i => $item)
			$items[$i] = RSMediaGalleryHelper::parseItem($item, $params);
	
	if ($use_original)
	{
		if ($items)
			foreach ($items as $item)
			{
				if ($item->params = $item->params ? unserialize($item->params) : array())
					$max_height = max($max_height, $item->params['info'][1]);
			}
	}
	else
	{
		$max_height = 0;
		if ($params->get('thumb_height') > 0)
			$max_height = (int) $params->get('thumb_height');
		else
		{
			if ($items)
				foreach ($items as $item)
				{
					if ($item->params = $item->params ? unserialize($item->params) : array()) {
						$max_height = max($max_height, $item->thumb_height);
					}
				}
		}
	}
		
	if ($show_pager)
		$document->addStyleDeclaration("
			#rsmg_carousel_pager".$module->id." {
				".($pager_position == 'bottom-inside' || $pager_position == 'top-inside' ? "position: absolute;" : "")."
				".($pager_position == 'bottom-inside' ? "bottom: 10px;" : "")."
				".($pager_position == 'top-inside' ? "top: 10px;" : "")."
				text-align: ".$pager_text_position." !important;
				".($pager_text_position == 'left' ? "left: 15px;" : "")."
				".($pager_text_position == 'right' ? "right: 15px;" : "")."
			}
		");

	if ($image_position != 'center')
		$document->addStyleDeclaration("
			#rsmg_carousel_container".$module->id." .rsmg_carousel_image {
				float: ".$image_position.";
				margin-".($image_position == 'left' ? 'right' : 'left').": 20px;
			}
		");
	else
		$document->addStyleDeclaration("
			#rsmg_slider".$module->id." > li {
				text-align: center;
			}
			#rsmg_carousel_container".$module->id." .rsmg_carousel_image {
				margin: 0 auto;
			}
		");

	if ($use_fixed_width)
	{
		$wrapper_width   = $fixed_width;
		$container_width = $fixed_width;
		
		// when controls are set, they add an inner 50px padding to the wrapper
		if ($show_controls && $fixed_width - 100 > 0)
		{
			$container_width = $fixed_width - 100;
			$wrapper_width   = $fixed_width - 100;
		}
		
		$document->addStyleDeclaration("
			#rsmg_carousel_wrapper".$module->id." {
				width: ".$wrapper_width."px;
			}
			#rsmg_carousel_container".$module->id." {
				width: ".$container_width."px;
			}
		");
	}
		
	$document->addScriptDeclaration("
		jQuery(document).ready(function($){
			$('#rsmg_slider".$module->id."').bxSlider({
				controls: ".$controls.",
				infiniteLoop: ".$infiniteLoop.",
				randomStart: ".$randomStart.",
				auto: ".$auto.",
				autoHover: ".$autoHover.",
				".($show_controls ? 
				"prevSelector: '#rsmg_carousel_prev".$module->id."',
				nextSelector: '#rsmg_carousel_next".$module->id."'," : "").
				"prevText: '',
				nextText: '',
				easing: '".$easing."',
				speed: ".$speed.",
				pause: ".$pause.",
				pager: ".$pager.",
				pagerSelector: $('#rsmg_carousel_pager".$module->id."'),
				pagerType: '".$pagerType."',
				hideControlOnEnd: true,
				mode: '".$mode."',
				childrenMaxHeight: '".$max_height."'
			});
		});
	");
	
	// Display template
	require JModuleHelper::getLayoutPath('mod_rsmediagallery_slideshow');
}
else
{
	JError::raiseWarning(500, JText::_('MOD_RSMEDIAGALLERY_SLIDESHOW_COMPONENT_NOT_INSTALLED_OR_UPDATED'));
}