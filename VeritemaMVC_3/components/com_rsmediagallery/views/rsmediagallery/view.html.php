<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSMediaGalleryViewRsmediagallery extends JViewLegacy
{
	public function display($tpl = null)
	{
		jimport('joomla.application.component.helper');
		
		require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/jquery.php';
		
		
		$mainframe 			= JFactory::getApplication();
		$document  			= JFactory::getDocument();
		$componentParams	= JComponentHelper::getParams('com_rsmediagallery');
		$jqueryHelper		= RSMediaGalleryjQuery::getInstance();
		
		$params    			= $this->get('params');
		$jversion  			= new JVersion();
		
		
		$this->tags 		= $this->get('Tags');
		$this->animation  	= $componentParams->get('animation',0);
		$this->responsive   = $params->get('responsive');
		$this->itemsrow     = $params->get('itemsrow');
		
		// update 1.7.5
		$this->itemsrow	= ($this->itemsrow > 6 ? 6 : ($this->itemsrow == 5 ? 4 : $this->itemsrow));
		
		
		// add jQuery library
		$jqueryHelper->addjQuery();

		$src = JDEBUG ? '.src' : '';
		
		$document->addScriptDeclaration("jQuery.noConflict();");
		$document->addScriptDeclaration("function rsmg_get_root() { return '".addslashes(rtrim(JURI::root(), '/'))."'; }");
		$document->addScriptDeclaration("rsmg_add_lang({'COM_RSMEDIAGALLERY_LOAD_MORE': '".JText::_('COM_RSMEDIAGALLERY_LOAD_MORE', true)."', 'COM_RSMEDIAGALLERY_LOAD_ALL': '".JText::_('COM_RSMEDIAGALLERY_LOAD_ALL', true)."', 'COM_RSMEDIAGALLERY_DOWNLOAD': '".JText::_('COM_RSMEDIAGALLERY_DOWNLOAD', true)."', 'COM_RSMEDIAGALLERY_TAGS': '".JText::_('COM_RSMEDIAGALLERY_TAGS', true)."', 'COM_RSMEDIAGALLERY_HIT': '".JText::_('COM_RSMEDIAGALLERY_HIT', true)."', 'COM_RSMEDIAGALLERY_HITS': '".JText::_('COM_RSMEDIAGALLERY_HITS', true)."', 'COM_RSMEDIAGALLERY_CREATED': '".JText::_('COM_RSMEDIAGALLERY_CREATED', true)."', 'COM_RSMEDIAGALLERY_MODIFIED': '".JText::_('COM_RSMEDIAGALLERY_MODIFIED', true)."'});");
		
		if ($this->animation) {
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
			
			$document->addScriptDeclaration("jQuery(function(){jQuery('#rsmg_gallery').mixitup({
					effects: ".$setEffects .",
					easing: '".$componentParams->get('animation_easing', 'smooth')."',
					transitionSpeed: ".$componentParams->get('animation_transision_speed', 600).",
					filterSelector: '#rsmg_gallery_filters .filter',
			});});");
		}
		
		
		$layout = $this->getLayout();
		
		
		$center_list = $params->get('center_list');
		$script = '';
		if ($center_list && $layout != 'image') {
			$script .= 'var center_list = true;';
		}
		else { 
			$script .= 'var center_list = false;';
		}
		
		if($this->responsive){
			$script .= 'var responsive = true;
			var itemsrow = '.$this->itemsrow.';';
		}
		else {
			$script .= 'var responsive = false;';
		}
		
		$document->addScriptDeclaration($script);
		
		

		$document->addScript(JURI::root(true).'/components/com_rsmediagallery/assets/js/jquery.script'.$src.'.js');
		if ($this->animation) $document->addScript(JURI::root(true).'/components/com_rsmediagallery/assets/js/jquery.mixitup.min'.$src.'.js');
		
		
		$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/style.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/'.($componentParams->get('contrast', 'light')).'.css');
		
		if($this->responsive && $componentParams->get('resposive_css', 1)) {
			$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/rsmg_responsive.css');
		}
		$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/style_'.($this->responsive ? 'responsive' : 'normal').'.css');
		
		
		
		
		
		if ($layout == 'image')
		{
			$this->item = $this->get('item');
			// not an item ?
			if (!$this->item)
			{
				JError::raiseError(500, JText::_('COM_RSMEDIAGALLERY_NOT_FOUND'));
				return;
			}
			
			if ($params->get('use_original', 0))
				$this->item->src = JURI::root(true).'/components/com_rsmediagallery/assets/gallery/original/'.$this->item->filename;
			else {
				$full_width  = $params->get('full_width', 800);
				$full_height = $params->get('full_height', 600);
				$this->item->src = RSMediaGalleryHelper::getImage($this->item, $full_width, $full_height, true);
			}
			
			// set page title
			$document->setTitle($document->getTitle().' - '.$this->item->title);
			
			// set breadcrumbs
			$pathway = $mainframe->getPathway();
			$pathway->addItem($this->item->title, '');
			
			// assign variables to the layout
			$this->adjacent = $this->get('adjacentitems');
			$this->params = $params;
			
			// don't forget to increase the views
			$model = $this->getModel('rsmediagallery');
			$model->hitItem($this->item->id);
		}
		else
		{
			$document->addScript(JURI::root(true).'/components/com_rsmediagallery/assets/js/jquery.lightbox2'.$src.'.js');
			$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/lightbox.css');
			
			$open_in = $params->get('open_in', 'slideshow');
			if ($open_in == 'slideshow')
			{
				$document->addScriptDeclaration("
				/* <![CDATA[ */
				function rsmg_init_lightbox2() {
					
					jQuery('#rsmg_gallery a.rsmg_lightbox').lightBox({
						imageCloseText: '".JText::_('COM_RSMEDIAGALLERY_CLOSE_LIGHTBOX', true)."',
						ajaxFunction: function(settings) {
							more 			= false;
							original_length = jQuery('ul#rsmg_gallery li .rsmg_item_container').length;
							
							if (jQuery('#rsmg_load_more').length > 0 && !jQuery('#rsmg_load_more').is(':hidden'))
							{
								rsmg_get_items(jQuery, false, {}, function(data) {
								
									new_length = jQuery('ul#rsmg_gallery li .rsmg_item_container').length;
									if (new_length - original_length == 0)
										more = false;
									else
									{
										var images = jQuery('#rsmg_gallery a.rsmg_lightbox');
										for (j = original_length; j<new_length; j++)
										{
											var currentImage = jQuery(images[j]);
											var rel = jQuery(currentImage).attr('rel');
											var href = currentImage.attr('src');
											var title = '';
											var id = 0;
											if (typeof rel != 'undefined' && rel.indexOf('{') > -1 && rel.indexOf('}') > -1)
											{
												eval('var decoded_rel = ' + rel + ';');
												if (typeof decoded_rel == 'object')
												{
													if (typeof decoded_rel.link != 'undefined')
														href = decoded_rel.link;
													
													if (typeof decoded_rel.id != 'undefined')
														id = decoded_rel.id;
												}
											}
											
											settings.addImage(settings, href, '#rsmg_item_' + j, id, 'all');
										}
										
										more = true;
									}
								}, false);
							}
							return more;
						},
						onImageLoad: rsmg_hit_item
					});
				}
				/* ]]> */
				");
			}
			
			$thumb_width = $params->get('thumb_width', 280);
			if ($thumb_width > 0 && !$this->responsive)
				$document->addStyleDeclaration('ul#rsmg_gallery li div { width: '.(int) $thumb_width.'px; }');
			
			$items = $this->get('items');
			if ($items)
			{
				foreach ($items as $i => $item)
					$items[$i] = RSMediaGalleryHelper::parseItem($item, $params, true);
				RSMediaGalleryHelper::addTags($items);
			}
			
			// assign variables to the layout
			$this->params 		= $params;
			$this->items 		= $items;
			$this->total 		= $this->get('total');
			$this->limitstart 	= $this->get('limitstart');
			$this->limit 		= $this->get('limit');
			$this->more 		= $this->limitstart + $this->limit < $this->total;
			$this->prev			= $this->limitstart > 0;
			$this->itemid		= $this->get('itemid');
			
			//var_dump($this->limitstart.'->'.$this->total);
		}
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	
	protected function _prepareDocument() {
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_RSMG_GALLERY'));
		}
		
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

}