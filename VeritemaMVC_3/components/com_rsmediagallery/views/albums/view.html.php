<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSMediaGalleryViewAlbums extends JViewLegacy
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
		
		$this->responsive   = $params->get('responsive');
		$this->itemsrow     = $params->get('itemsrow');
		
		// update 1.7.5
		$this->itemsrow	= ($this->itemsrow > 6 ? 6 : ($this->itemsrow == 5 ? 4 : $this->itemsrow));
		
		// add jQuery library
		$jqueryHelper->addjQuery();
		

		$src = JDEBUG ? '.src' : '';
		
		$document->addScriptDeclaration("jQuery.noConflict();");
		$document->addScriptDeclaration("function rsmg_get_root() { return '".addslashes(rtrim(JURI::root(), '/'))."'; }");
		
		
		
		$script = 'var center_list = false;';
		
		$center_albums = $params->get('center_albums');
		
		if ($center_albums) {
			$script .= 'var center_albums = true;';
		}
		else { 
			$script .= 'var center_albums = false;';
		}
		
		if($this->responsive){
			$script .= 'var responsive = true;
			var itemsrow = '.$this->itemsrow.';
			var albumview = true;';
		}
		else {
			$script .= 'var responsive = false;
			var albumview = true;';
		}
		
		$document->addScriptDeclaration($script);
		

		$document->addScript(JURI::root(true).'/components/com_rsmediagallery/assets/js/jquery.script'.$src.'.js');
		$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/style.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/'.($componentParams->get('contrast', 'light')).'.css');
		
		if($this->responsive && $componentParams->get('resposive_css',1)) {
			$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/rsmg_responsive.css');
		}
		
		$document->addStyleSheet(JURI::root(true).'/components/com_rsmediagallery/assets/css/style_'.($this->responsive ? 'responsive' : 'normal').'.css');
		
		$allMenu 	= $mainframe->getMenu();
		$activeMenu = $allMenu->getActive();
		$this->albumName = $activeMenu->title;
		
		$this->tags = $this->get('Tags');
		$model = $this->getModel('albums');
		
		$items = $this->get('TagItems');
		
		$this->preview_res = $params->get('display_preview_resolution');
		$referenceW = ($this->preview_res[1] * 2) / 4;
		$album_image_w = array();
		
		foreach ($items as $tag=>$itemsTag) {
			$nrItems = count($itemsTag);
			
			foreach ($itemsTag as $i => $item) {
				if ( $nrItems==1 ) {
					$innerThumbH = $innerThumbW = $referenceW * 2;
				} else if ( $nrItems==2 ) {
					$innerThumbW = $referenceW;
					$innerThumbH = $referenceW * 2;
				} else {
					$innerThumbH = $innerThumbW = $referenceW;
					
				}
				$items[$tag][$i] = $model->parseItemAlbum($item, $innerThumbW, $innerThumbH);
				$album_image_w[$tag] = $innerThumbW;
			}
		}
		
		if (count($album_image_w) > 0) {
			$decoded_tags = array();
			foreach($album_image_w as $tag=>$dimension) {
				$decoded_tags[RSMediaGalleryHelper::niceTag($this->escape($tag))] = $dimension;
			}
			$album_image_w = json_encode($decoded_tags);
			
			$document->addScriptDeclaration('var album_image_data = {};
			album_image_data = '.$album_image_w.';');
		}
		
		$this->items = $items;
		
		$this->links  = $model->buildAlbumLinks($this->tags);
		$this->params = $params;
		
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
			$this->params->def('page_heading', JText::_('COM_RSMG_ALBUMS'));
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