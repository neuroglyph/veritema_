<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

function RSMediaGalleryBuildRoute(&$query)
{
	$lang = JFactory::getLanguage();
	$lang->load('com_rsmediagallery', JPATH_SITE);
	
	$segments = array();
	
	if (isset($query['view']))
	{
		if (!isset($query['layout'])) $query['layout'] = 'default';
		
		switch ($query['view'])
		{
			case 'rsmediagallery':
				switch ($query['layout'])
				{
					case 'image':
						$segments[] = JText::_('COM_RSMEDIAGALLERY_SEF_VIEW_IMAGE');
						$segments[] = isset($query['id']) ? $query['id'] : 0;
						$segments[] = isset($query['ext']) ? $query['ext'] : 'jpg';
						unset($query['id'], $query['ext']);
					break;
				}
			break;
			case 'album':
				switch ($query['layout'])
				{	
					case 'default':
						$segments[] = JText::_('COM_RSMEDIAGALLERY_SEF_VIEW_CATEGORY');
						$segments[] = isset($query['tag']) ? urlencode('tag-'.$query['tag']) : 0;
						unset($query['tag']);
					break;
					case 'image':
						$segments[] = JText::_('COM_RSMEDIAGALLERY_SEF_VIEW_IMAGE_ALBUM');
						$segments[] = isset($query['tag']) ? urlencode('tag-'.$query['tag']) : 0;
						$segments[] = isset($query['id']) ? $query['id'] : 0;
						$segments[] = isset($query['ext']) ? $query['ext'] : 'jpg';
						unset($query['tag'], $query['id'], $query['ext']);
					break;
				}
			break;
		}
		
		unset($query['view'], $query['layout']);
	}
	
	if (isset($query['task']))
	{
		switch ($query['task'])
		{
			case 'getitems':
				$segments[] = JText::_('COM_RSMEDIAGALLERY_SEF_GET_ITEMS');
				unset($query['format']);
			break;
			
			case 'downloaditem':
				$segments[] = JText::_('COM_RSMEDIAGALLERY_SEF_DOWNLOAD_IMAGE');
				$segments[] = isset($query['id']) ? $query['id'] : 0;
				$segments[] = isset($query['ext']) ? $query['ext'] : 'jpg';
				unset($query['id'], $query['ext']);
			break;
			
			case 'createthumb':
				$segments[] = JText::_('COM_RSMEDIAGALLERY_SEF_CREATE_THUMB');
				$segments[] = isset($query['resolution']) ? $query['resolution'] : '280x210';
				$segments[] = isset($query['id']) ? $query['id'] : 0;
				$segments[] = isset($query['ext']) ? $query['ext'] : 'jpg';
				unset($query['id'], $query['resolution'], $query['ext']);
			break;
			case 'createthumbzc':
				$segments[] = JText::_('COM_RSMEDIAGALLERY_SEF_CREATE_THUMB_ZC');
				$segments[] = isset($query['resolution']) ? $query['resolution'] : '280x210';
				$segments[] = isset($query['id']) ? $query['id'] : 0;
				$segments[] = isset($query['ext']) ? $query['ext'] : 'jpg';
				unset($query['id'], $query['resolution'], $query['ext']);
			break;
		}
		
		unset($query['task']);
	}
	return $segments;
}

function RSMediaGalleryParseRoute($segments)
{
	$lang = JFactory::getLanguage();
	$lang->load('com_rsmediagallery', JPATH_SITE);
	
	$query = array();
	
	$segments[0] = str_replace(':', '-', $segments[0]);
	
	switch ($segments[0])
	{
		case JText::_('COM_RSMEDIAGALLERY_SEF_VIEW_IMAGE'):
			$query['view']   = 'rsmediagallery';
			$query['layout'] = 'image';
			$query['id']	 = isset($segments[1]) ? $segments[1] : 0;
			$query['ext']	 = isset($segments[2]) ? $segments[2] : 'jpg';
		break;
		
		case JText::_('COM_RSMEDIAGALLERY_SEF_GET_ITEMS'):
			$query['task']	 = 'getitems';
			$query['format'] = 'raw';
		break;
		
		case JText::_('COM_RSMEDIAGALLERY_SEF_DOWNLOAD_IMAGE'):
			$query['task']	 = 'downloaditem';
			$query['id']	 = isset($segments[1]) ? $segments[1] : 0;
			$query['ext']	 = isset($segments[2]) ? $segments[2] : 'jpg';
		break;
		
		case JText::_('COM_RSMEDIAGALLERY_SEF_CREATE_THUMB'):
			$query['task']	 	 = 'createthumb';
			$query['resolution'] = isset($segments[1]) ? $segments[1] : 'thumb';
			$query['id']	 	 = isset($segments[2]) ? $segments[2] : 0;
			$query['ext']	 	 = isset($segments[3]) ? $segments[3] : 'jpg';
		break;
		
		case JText::_('COM_RSMEDIAGALLERY_SEF_CREATE_THUMB_ZC'):
			$query['task']	 	 = 'createthumbzc';
			$query['resolution'] = isset($segments[1]) ? $segments[1] : 'thumb';
			$query['id']	 	 = isset($segments[2]) ? $segments[2] : 0;
			$query['ext']	 	 = isset($segments[3]) ? $segments[3] : 'jpg';
		break;
		
		case JText::_('COM_RSMEDIAGALLERY_SEF_VIEW_CATEGORY'):
			$query['view']   = 'album';
			$query['tag']	 = isset($segments[1]) ? str_replace('tag:', '', $segments[1]) : 0;
		break;
		
		case JText::_('COM_RSMEDIAGALLERY_SEF_VIEW_IMAGE_ALBUM'):
			$query['view']   = 'album';
			$query['layout'] = 'image';
			$query['tag']	 = isset($segments[1]) ? str_replace('tag:', '', $segments[1]) : 0;
			$query['id']	 = isset($segments[2]) ? $segments[2] : 0;
			$query['ext']	 = isset($segments[3]) ? $segments[3] : 'jpg';
		break;
	}
	
	return $query;
}