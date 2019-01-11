<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSMediaGalleryViewAlbum extends JViewLegacy
{
	public function display($tpl = null)
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
		
		if (!function_exists('json_encode'))
			require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/jsonwrapper/json.php';
		
		$document  = JFactory::getDocument();
		$params    = $this->get('AlbumParams');
		
		// set encoding
		$document->setMimeEncoding('application/json');
		
		// layout
		$layout = $this->getLayout();
		if ($layout == 'items')
		{
			$items = $this->get('items');
		
			if ($items)
			{
				foreach ($items as $i => $item)
					$items[$i] = RSMediaGalleryHelper::parseItem($item, $params, false);
				RSMediaGalleryHelper::addTags($items);
			}
					
			$this->result = $this->_getResult($items, $this->get('total'));
		}
		else
		{
			JError::raiseError(500, JText::_('COM_RSMEDIAGALLERY_NOT_FOUND'));
			return;
		}
		
		parent::display($tpl);
	}
	
	protected function _getResult($items, $total)
	{
		$result = new stdClass();
		
		$result->items = $items;
		$result->total = $total;
		return $result;
	}
}