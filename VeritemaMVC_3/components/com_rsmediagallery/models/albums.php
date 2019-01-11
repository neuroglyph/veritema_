<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMediaGalleryModelAlbums extends JModelLegacy
{
	protected $_params = null;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_params 	= $this->getParams();
	}
	
	
	public function _buildTags()
	{
		
		$tags = explode(',', $this->_params->get('tags'));
		if (end($tags) == '')
			array_pop($tags);
		
		if ($this->_buildTagDirection()=='ASC') {
			sort($tags);
		} else {
			rsort($tags);
		}
		return $tags;
	}
	
	public function _buildTagDirection()
	{
		$direction 	= strtoupper($this->_params->get('direction_album', 'asc'));
		
		return $direction;
	}
	
	public function getTags() {
		$data =  $this->_buildTags();
		return $data;
	}
	
	public function getTagItems()
	{	
		static $items;
		$tags = $this->getTags();
		
		foreach ($tags as $tag) {
			if (empty($items[$tag])) {
				$db = JFactory::getDBO();
				
				$query = "SELECT DISTINCT(i.id), i.filename, i.params FROM #__rsmediagallery_items i LEFT JOIN #__rsmediagallery_tags t ON (i.id=t.item_id) WHERE i.published='1' AND t.tag='".$db->escape(trim($tag))."' LIMIT 4";
				$db->setQuery($query);
				$items[$tag] = $db->loadObjectList();
			}
		}
		return $items;
	}
	
	public function parseItemAlbum(&$item, $width, $height){
		require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
		$item->thumb = RSMediaGalleryHelper::getImage($item, $width, $height);
		return $item;
	}
	
	public function buildAlbumLinks($tags) {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
		static $links = array();
		
		foreach ($tags as $tag) {
			if (!isset($links[$tag])) {
				$links[$tag] = RSMediaGalleryRoute::_('index.php?option=com_rsmediagallery&view=album&tag='.urlencode($tag));
			}
		}
		return $links;
	}
	

	public function getParams()
	{
		static $params;
		if (empty($params))
		{
			require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
			
			$mainframe 	= JFactory::getApplication();
			$params 	= RSMediaGalleryHelper::parseParams($mainframe->getParams('com_rsmediagallery'));
		}
		
		return $params;
	}
}