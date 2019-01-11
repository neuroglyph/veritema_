<?php
/**
* @version 1.0.0
* @package RSMediaGallery! 1.0.0
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

class plgSystemRSMediaGallery extends JPlugin
{
	protected $_buffer;
	protected $_css 		= array();
	protected $_js 			= array();
	protected $_js_inline 	= array();
	
	protected function _addStyleSheet($file, $fullpath=false) {
		static $template;
		
		if (empty($template)) {
			$app	  = JFactory::getApplication();
			$template = $app->getTemplate();
		}
		
		if ($fullpath) {
			$path = $file;
		} else {
			if (file_exists(JPATH_SITE.'/templates/'.$template.'/html/plg_system_rsmediagallery/assets/css/'.$file)) {
				$path = JURI::root(true).'/templates/'.$template.'/html/plg_system_rsmediagallery/assets/css/'.$file;
			} else {
				$path = JURI::root(true).'/plugins/system/rsmediagallery/assets/css/'.$file;
			}
		}
		
		$this->_css[] = '<link rel="stylesheet" href="'.$this->escape($path).'" type="text/css" />';
	}
	
	protected function _addScript($file, $fullpath=false) {
		if ($fullpath) {
			$path = $file;
		} else {
			$path = JURI::root(true).'/plugins/system/rsmediagallery/assets/js/'.$file;
		}
		
		$this->_js[] = '<script type="text/javascript" src="'.$this->escape($path).'"></script>';
	}
	
	protected function _addScriptDeclaration($declaration)
	{
		$this->_js_inline[] = $declaration;
	}
	
	protected function _cleanUp()
	{
		$this->_buffer = str_replace('</head>', implode("\r\n", $this->_js)."\r\n".'<script type="text/javascript">'.implode("\r\n", $this->_js_inline).'</script>'."\r\n".implode("\r\n", $this->_css)."\r\n".'</head>', $this->_buffer);
		JResponse::setBody($this->_buffer);
		unset($this->_buffer, $this->_css, $this->_js, $this->_js_inline);
	}
	
	public function __construct( &$subject, $params )
	{
		parent::__construct( $subject, $params );
		
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php'))
		{
			require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';			
			$this->params = RSMediaGalleryHelper::parseParams($this->params);
		}
	}
	
	public function onAfterRender()
	{
		$mainframe = JFactory::getApplication();
		
		// we do not have to run in the admin section
		if ($mainframe->isAdmin())
			return true;
		
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php') || !file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/jquery.php'))
		{
			$lang = JFactory::getLanguage();
			$lang->load('plg_system_rsmediagallery', JPATH_ADMINISTRATOR);
			JError::raiseWarning(500, JText::_('PLG_SYSTEM_RSMEDIAGALLERY_SYSTEM_PLUGIN_COMPONENT_NOT_INSTALLED_OR_UPDATED'));
			return true;
		}
		
		$this->_buffer = JResponse::getBody();
			
		// simple performance check to determine whether bot should process further
		if (strpos($this->_buffer, '{rsmediagallery') === false)
		{
			unset($this->_buffer);
			return true;
		}
		
		$pattern = '/{rsmediagallery\s+(.*?)}/i';
		if (preg_match_all($pattern, $this->_buffer, $matches, PREG_SET_ORDER))
		{
			jimport('joomla.application.component.helper');
			
			require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/jquery.php';
			
			$componentParams = JComponentHelper::getParams('com_rsmediagallery');
			$jqueryHelper 	 = RSMediaGalleryjQuery::getInstance();
			$animation		 = $this->params->get('animation');
			$responsive		 = $this->params->get('responsive');
			$itemsrow		 = $this->params->get('itemsrow');
			$center_list	 = $this->params->get('center_list');
			
			// update 1.7.5
			$itemsrow	= ($itemsrow > 6 ? 6 : ($itemsrow == 5 ? 4 : $itemsrow));
			$spanType 	= floor(12 / $itemsrow);
			
			// handle template overrides
			$this->_addStyleSheet('style.css');
			$this->_addStyleSheet($componentParams->get('contrast', 'light').'.css');
			
			$script = '';
			if ($responsive) {
				$this->_addStyleSheet('rsmg_responsive.css');
				$this->_addStyleSheet('style_responsive.css');
				
				$script .= 'var responsive_system = true;
				var itemsrow_system = '.$itemsrow.';';
			}
			else {
				$this->_addStyleSheet('style_normal.css');
				$script .= 'var responsive_system = false;';
			}
			
			if($center_list) {
				$script .= 'var center_list_system = true;';
			}
			else {
				$script .= 'var center_list_system = false;';
			}
			
			$this->_addScriptDeclaration($script);
			
			$src = JDEBUG ? '.src' : '';
			
			// JS
			$this->_buffer = $jqueryHelper->replacejQuery($this->_buffer);

			$this->_addScriptDeclaration("jQuery.noConflict();");
			$this->_addScript('jquery.pirobox'.$src.'.js');
			$this->_addScript('jquery.script'.$src.'.js');
				
			if ($animation) {
				
				$this->_addScript('jquery.mixitup.min'.$src.'.js');
				
				$effects = array('fade','scale','rotatex','rotatez','rotatey','blur','greyscale');
				$setEffects = array();
				foreach ($effects as $effect) {
					if($componentParams->get('animation_effect_'.$effect)) {
						if ($effect=='rotatex') $effect = 'rotateX';
						if ($effect=='rotatez') $effect = 'rotateZ';
						if ($effect=='rotatey') $effect = 'rotateY';
						$setEffects[] = $effect;
					}
				}
				if (count($setEffects)==0) $setEffects = array('fade','scale');
				$setEffects = json_encode($setEffects);
			}
			
			require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
			jimport('joomla.html.parameter');
		
			foreach ($matches as $index => $match)
			{
				$match[1]   = html_entity_decode($match[1]);
				$attributes = JUtility::parseAttributes($match[1]);
				$registry 	= JRegistry::getInstance(md5($match[1]));
				$registry->loadArray($attributes);
				$this->params->set('tags', $registry->get('tags'));
				$uniqId 	= uniqid();
				
				$tmp_params = clone($this->params);

				// can't have both, it would look distorted
				if ($registry->get('thumb_width') > 0)
					$registry->set('thumb_height', 0);
				elseif ($registry->get('thumb_height') > 0)
					$registry->set('thumb_width', 0);

				$tmp_params->merge($registry);
				
				$this->_buffer = str_replace($match[0], $this->_getGallery($index, $tmp_params, $match[0], $animation, $responsive, $itemsrow, $uniqId), $this->_buffer);
				if ($animation) {
					$this->_addScriptDeclaration("jQuery(function(){jQuery('.rsmg_".$uniqId." .rsmg_system_gallery').mixitupsystem({
						effects: ".$setEffects .",
						easing: '".($componentParams->get('animation_easing') !='' ? $componentParams->get('animation_easing') : 'smooth')."',
						transitionSpeed: ".($componentParams->get('animation_transision_speed') !='' ? $componentParams->get('animation_transision_speed') : 600).",
						filterSelector: '.rsmg_gal".$index." .rsmg_system_gallery_filters .filter',
						masterGallery: '.rsmg_gal".$index."'
				});});");
				}	
			}
			
			$this->_cleanUp();
			
			return true;
		}
	}
	
	protected function _getGallery($index, $params, $text, $animation, $responsive, $itemsrow, $uniqId) {
	
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsmediagallery', JPATH_ADMINISTRATOR);
		
		$html = '';
		
		$tags = $params->get('tags');
		if (!$tags)
			return $text;
			
		$order 				= $params->get('ordering', $this->params->get('ordering', 'ordering'));
		$direction			= $params->get('direction', $this->params->get('direction', 'ASC'));
		$limit				= (int) $params->get('limit', $this->params->get('limit', 0));
		$show_title			= (int) $params->get('show_title', $this->params->get('show_title', 1));
		$show_description 	= (int) $params->get('show_description', $this->params->get('show_description', 1));
		$use_original 		= (int) $params->get('use_original', 0);
		$image				= (int) $params->get('image', 0);
		
		$items = RSMediaGalleryHelper::getItems($tags, $order, $direction, 0, $limit);
		
		if ($items)
		{
			$html .= '<div class="'.($responsive ? 'rsmg_gallery ': '').'rsmg_'.$uniqId.' rsmg_gal'.$index.'">';
			
			if ($animation) {
				$usedTags = explode(',',$tags);
				
				if ($animation) {
					$html .= '<ul class="rsmg_system_gallery_filters">
						<li class="filter active" data-filter="all">'.JText::_('PLG_SYSTEM_RSMEDIAGALLERY_ALL').'</li>';
						foreach ( $usedTags as $tag ) { 
							$html .= '<li class="filter" data-filter="'.$this->niceTag($tag).'" style="display:none;">'.$this->escape($tag).'</li>';
						 }
					$html .= '</ul>';
				}
			}
			
			$html .= '<ul class="rsmg_system_gallery'.($responsive ? ' row-fluid':'').'">';
			$spanType = floor(12 / $itemsrow);
			
			RSMediaGalleryHelper::addTags($items);
			$nr = 0;
			foreach ($items as $i => $item)
			{
				$margin_class = '';
				if ($responsive) {
					if ($nr % $itemsrow == 0) {
						$margin_class = 'margin_left_none';
					}
				}
				
				$item 			= RSMediaGalleryHelper::parseItem($item, $params);
				
				$small_image 	= $item->thumb;
				$big_image   	= $item->full;
				$thumb_width 	= $item->thumb_width;
				$thumb_height 	= $item->thumb_height;
				
				$title			= '';
				if ($show_title || $show_description)
				{
					if ($show_title)
						$title .= '<b>'.$item->title.'</b>';
					if ($show_description)
						$title .= ($show_title ? '<br />' : '').$item->full_description;
					$title = ' title="'.$this->escape($title).'"';
				}
				
				$html .= (!$image || $image && $i+1 == $image) ? '<li'.($animation ? ' class="'.($responsive? 'span'.$spanType.' '.$margin_class.' ' : '').'mix '.$item->niceTags.'"' : ($responsive? ' class="span'.$spanType.' '.$margin_class.'"' : '')).'>' : '<li style="display: none;">';
				$html .= '<div class="rsmg_system_container">';
				$html .= '<a href="'.$big_image.'" rel="gallery" data-filter="all" class="pirobox_gall_system'.$index.' rs_plg_system"'.$title.'><img src="'.$small_image.'" '.(!$responsive ? 'width="'.$thumb_width.'" height="'.$thumb_height.'" style="width:'.$thumb_width.'px; height:'.$thumb_height.'px;"' : '').' alt="'.$this->escape($item->title).'" /></a>';
				$html .= '</div>';
				$html .= '</li>';
				$nr++;
			}
			$html .= '</ul>';
			$html .= '<span class="rsmg_system_clear"></span>';
			$html .= '</div>';
		}
		
		return $html;
	}
	
	protected function niceTag($tag)
	{
		return RSMediaGalleryHelper::niceTag($tag);
	}
	
	protected function escape($string) {
		return RSMediaGalleryHelper::escape($string);
	}
}