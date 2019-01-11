<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMediaGalleryModelRSMediaGallery extends JModelLegacy
{
	protected $_params = null;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_db 		= JFactory::getDBO();
		$this->_params 	= $this->getParams();
	}
	
	public function _buildDataQuery()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
		
		$tags  		= $this->_buildTags();
		$order 		= $this->_buildOrder();
		$direction  = $this->_buildDirection();
		
		return RSMediaGalleryHelper::getItemsQuery($tags, $order, $direction);
	}
	
	public function _buildAdjacentQuery($what)
	{
		$tags  		= $this->_buildTags();
		$order 		= $this->_buildOrder();
		$direction  = $this->_buildDirection();
		$item		= $this->getItem();
		
		$where = array();
		foreach ($tags as $tag)
			$where[] = "t.tag='".$this->_db->escape($tag)."'";
		
		if ($what == 'prev')
		{
			$sign 			= $direction == 'ASC' ? '<' : '>';
			$our_direction 	= $direction == 'ASC' ? 'DESC' : 'ASC';
		}
		else
		{
			$sign 			= $direction == 'ASC' ? '>' : '<';
			$our_direction 	= $direction == 'ASC' ? 'ASC' : 'DESC';
		}
		
		$our_ordering = $this->_params->get('ordering', 'ordering');
		if (in_array($our_ordering, array('created', 'modified'))) {
			$our_ordering = $our_ordering.'Date';
		}
		
		$value = isset($item->{$our_ordering}) ? $item->{$our_ordering} : '';
		
		$query = "SELECT DISTINCT(i.id), i.filename FROM #__rsmediagallery_items i LEFT JOIN #__rsmediagallery_tags t ON (i.id=t.item_id) WHERE i.published='1' AND (".implode(' OR ', $where).") AND ".$this->_db->escape($order)." ".$sign." '".$this->_db->escape($value)."' ORDER BY ".$this->_db->escape($order)." ".$this->_db->escape($our_direction)." LIMIT 1";
		
		return $query;
	}

	public function _buildPrevQuery()
	{
		return $this->_buildAdjacentQuery('prev');
	}
	
	public function _buildNextQuery()
	{
		return $this->_buildAdjacentQuery('next');
	}
	
	public function _buildTags()
	{
		$tags = explode(',', $this->_params->get('tags'));
		if (end($tags) == '')
			array_pop($tags);
		
		return $tags;
	}
	
	public function _buildOrder()
	{
		$order = $this->_params->get('ordering', 'ordering');
		$order = $order == 'tags' ? $order : 'i.'.$order;
		
		return $order;
	}
	
	public function _buildDirection()
	{
		$direction 	= strtoupper($this->_params->get('direction', 'asc'));
		
		return $direction;
	}
	
	public function getTags() {
		$data =  $this->_buildTags();
		return $data;
	}
	
	public function getItems()
	{	
		$limitstart = $this->getLimitStart();
		$limit		= $this->getLimit();

		if (empty($this->_data))
			$this->_data = $this->_getList($this->_buildDataQuery(), $limitstart, $limit);
		
		return $this->_data;
	}
	
	public function getTotal()
	{
		if (empty($this->_total))
			$this->_total = $this->_getOptimizedListCount($this->_buildDataQuery(), 'COUNT(DISTINCT(i.id))');
		
		return $this->_total;
	}
	
	public function _getOptimizedListCount($query, $count='COUNT(*)')
	{
		if (strpos($query,'FROM') !== false)
		{
			$tmp   = explode('FROM', $query, 2);
			$query = "SELECT $count FROM ".$tmp[1];
			
			$tmp 	= explode('ORDER BY', $query, 2);
			$query  = $tmp[0];
			
			$tmp 	= explode('GROUP BY', $query, 2);
			$query  = $tmp[0];
			
			$this->_db->setQuery($query);			
			return $this->_db->loadResult($query);
		}
		
		return $this->_getListCount($query);
	}
	
	public function getLimitStart()
	{
		return JRequest::getInt('limitstart', 0);
	}
	
	public function getLimit()
	{
		if (JRequest::getInt('limitall'))
			return JRequest::getInt('limit', 0);
		else
			return $this->_params->get('limit', 5);
	}
	
	public function getItemId()
	{
		return JRequest::getInt('Itemid', 0);
	}
	
	public function _getId()
	{
		return JRequest::getCmd('id', 0).'.'.JRequest::getCmd('ext', 'jpg');
	}
	
	public function getItem()
	{
		if (empty($this->_item))
		{
			$id = $this->_getId();
			
			// get item
			$this->_db->setQuery("SELECT * FROM #__rsmediagallery_items WHERE `filename`='".$this->_db->escape($id)."' AND `published`='1'");
			$this->_item = $this->_db->loadObject();
			
			if ($this->_item)
			{
				// get file & extension
				jimport('joomla.filesystem.file');
				$this->_item->filepart = JFile::stripExt($this->_item->filename);
				$this->_item->fileext  = JFile::getExt($this->_item->filename);
				
				// convert params
				$this->_item->params = $this->_item->params ? @unserialize($this->_item->params) : array();
				
				// correct date
				$this->_item->modifiedDate = $this->_item->modified;
				$this->_item->createdDate  = $this->_item->created;
				if ($this->_item->modified != $this->_db->getNullDate() && $this->_item->modified != $this->_item->created)
					$this->_item->modified = JHTML::_('date',  $this->_item->modified, JText::_('DATE_FORMAT_LC2'));
				else
					$this->_item->modified = JText::_('COM_RSMEDIAGALLERY_NEVER');
					
				if ($this->_item->created != $this->_db->getNullDate())
					$this->_item->created = JHTML::_('date',  $this->_item->created, JText::_('DATE_FORMAT_LC2'));
				else
					$this->_item->created = JText::_('COM_RSMEDIAGALLERY_NEVER');
				
				$this->_item->description = str_replace('{readmore}', '', $this->_item->description);

				// append tags
				$this->_db->setQuery("SELECT `tag` FROM #__rsmediagallery_tags WHERE `item_id`='".$this->_item->id."' ORDER BY `tag` ASC");
				$this->_item->tags = implode(', ', $this->_db->loadColumn());
			}
		}
		
		// return
		return $this->_item;
	}
	
	public function getAdjacentItems()
	{
		if (empty($this->_adjacent))
		{
			$this->_adjacent = (object) array(
				'prev' => false,
				'next' => false
			);
			
			// get data
			// nasty, but works at the moment.
			$query = $this->_buildDataQuery();
			$this->_db->setQuery($query);
			
			$sortedArray = $this->_db->loadObjectList('id');
			
			// get item
			$item = $this->getItem();
			if (!empty($sortedArray[$item->id])) {
				$sortedKeys 	= array_keys($sortedArray);
				$sortedValues 	= array_values($sortedArray);
				
				$pos = array_search($item->id, $sortedKeys);
				$prevPos = $pos-1;
				if (isset($sortedKeys[$prevPos])) {
					$prev = $sortedValues[$prevPos];
					// get file & extension
					jimport('joomla.filesystem.file');
					
					$this->_adjacent->prev = (object) array(
						'filepart' => JFile::stripExt($prev->filename),
						'fileext'  => JFile::getExt($prev->filename)
					);
				}
				$nextPos = $pos+1;
				if (isset($sortedKeys[$nextPos])) {
					$next = $sortedValues[$nextPos];
					// get file & extension
					jimport('joomla.filesystem.file');
					
					$this->_adjacent->next = (object) array(
						'filepart' => JFile::stripExt($next->filename),
						'fileext'  => JFile::getExt($next->filename)
					);
				}
				
				unset($sortedArray, $sortedKeys, $sortedValues);
			}
		}
		
		return $this->_adjacent;
	}
	
	public function hitItem($cid)
	{
		// if it's array, update several
		if (is_array($cid))
		{
			JArrayHelper::toInteger($cid);
			$this->_db->setQuery("UPDATE #__rsmediagallery_items SET `hits`=`hits` + 1 WHERE `id` IN (".implode(",", $cid).")");
		}
		// just update one
		else
			$this->_db->setQuery("UPDATE #__rsmediagallery_items SET `hits`=`hits` + 1 WHERE `id`='".(int) $cid."'");
			
		// run the query
		return $this->_db->query();
	}
	
	public function downloadItem()
	{
		if ($item = $this->getItem())
		{
			@ob_end_clean();
			$filter = ($item->params['filters'] != '' ? 'filter/' : '');

			$path = JPATH_SITE.'/components/com_rsmediagallery/assets/gallery/original/'.$filter.$item->filename;
			
			header("Cache-Control: public, must-revalidate");
			header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			if (strstr(@$_SERVER["HTTP_USER_AGENT"],"MSIE")==false) {
				header("Cache-Control: no-cache");
				header("Pragma: no-cache");
			}
			header("Expires: 0"); 
			header("Content-Description: File Transfer");
			header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
			header("Content-Type: application/octet-stream; charset=utf-8");
			header("Content-Length: ".(string) filesize($path));
			header('Content-Disposition: attachment; filename="'.$item->original_filename.'"');
			header("Content-Transfer-Encoding: binary\n");
			@readfile($path);
		}

		jexit();
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