<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSMediaGalleryController extends JControllerLegacy
{
	public function __construct()
	{
		parent::__construct();
	}

	public function createThumb()
	{
		
		$res	 = JRequest::getCmd('resolution');
		$hash	 = md5($res.JPATH_SITE);
		$db 	 = JFactory::getDBO();
		
		
		$db->setQuery("SELECT * FROM #__rsmediagallery_thumbs WHERE ".$db->qn('hash').'='.$db->q($hash));		
		if ($db->loadResult())
		{
			$model	   	 			= $this->getModel('rsmediagallery');
			$mainframe 	 			= JFactory::getApplication();
			
			$admin_thumb 			= $res == '280x210';
			list($width, $height) 	= explode('x', $res);

			// get item from database
			$db->setQuery("SELECT * FROM #__rsmediagallery_items WHERE `filename`='".$db->escape($model->_getId())."'");
			if ($item = $db->loadObject())
			{
				$id 			  = $item->filename;
				$item->params	  = unserialize($item->params);
				$upload_location  = JPATH_SITE.'/components/com_rsmediagallery/assets/gallery';
				$thumb_location	  = $admin_thumb ? $upload_location : $upload_location.'/'.$res;
				$isFilter		  = (isset($item->params['filters']) && $item->params['filters'] != '');
				
				// verify prev filter hash
				if(isset($item->params['filters'])) {
					$hashFilter 	  = md5($res.$item->params['filters']);
					$db->setQuery("SELECT hash FROM #__rsmediagallery_effects WHERE ".$db->qn('item_id').'='.$db->q($item->id)." AND ".$db->qn('hash').'='.$db->q($hashFilter));
					$isNewFilter     = !$db->loadResult();
				}
				else $isNewFilter = false;
				
				// cached file does not exist - must recreate it
				if (!file_exists($thumb_location.'/'.$id) || ($isFilter && (($isNewFilter && file_exists($thumb_location.'/filter/'.$id)) || !file_exists($thumb_location.'/filter/'.$id))))
				{
					require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/phpthumb/phpthumb.class.php';
					
					$phpThumb = new phpThumb();
					$phpThumb->src = $upload_location.'/original/'.$id;
					$phpThumb->w   = $width;
					$phpThumb->h   = $height;
					$phpThumb->iar = 1;
					$phpThumb->zc  = 0;
					
					jimport('joomla.filesystem.file');
					$fileExtension  = JFile::getExt($id);

					if ($fileExtension=='png' || $fileExtension=='gif') {
						$phpThumb->fltr = 'stc|FFFFFF|5|10';
						$phpThumb->f   = $fileExtension;
					}
					
					$ratio = $item->params['info'][0] / 380;
					
					$phpThumb->sx = round($item->params['selection']['x1']*$ratio);
					$phpThumb->sy = round($item->params['selection']['y1']*$ratio);
					$phpThumb->sw = round(($item->params['selection']['x2'] - $item->params['selection']['x1'])*$ratio);
					$phpThumb->sh = round(($item->params['selection']['y2'] - $item->params['selection']['y1'])*$ratio);
					
					// bug fix
					if ($phpThumb->sx == 1 && $phpThumb->sw == $item->params['info'][0] - 1)
					{
						$phpThumb->sx = 0;
						$phpThumb->sw = $item->params['info'][0];
					}
					if ($phpThumb->sy == 1 && $phpThumb->sh == $item->params['info'][1] - 1)
					{
						$phpThumb->sy = 0;
						$phpThumb->sh = $item->params['info'][1];
					}
					
					if ($phpThumb->GenerateThumbnail())
					{
						if (!$admin_thumb && !is_dir($thumb_location))
						{
							jimport('joomla.filesystem.file');
							jimport('joomla.filesystem.folder');
							
							// set folder permissions
							$componentParams = JComponentHelper::getParams('com_rsmediagallery');
							$perms 			 = octdec('0'.$componentParams->get('folder_perms', '755'));
							
							if (JFolder::create($thumb_location, $perms))
							{
								$buffer = '<html><body bgcolor="#FFFFFF"></body></html>';
								JFile::write($thumb_location.'/index.html', $buffer);
							}
						}
						if ($phpThumb->RenderToFile($thumb_location.'/'.$id))
						{
							// set permissions as setup in the configuration screen
							$componentParams = JComponentHelper::getParams('com_rsmediagallery');
							$perms 			 = octdec('0'.$componentParams->get('file_perms', '644'));
							@chmod($thumb_location.'/'.$id, $perms);
							
							if($isFilter) {
								require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
								// copy the image with the proper effects
								RSMediaGalleryHelper::applyFilters($item->params['filters'], $id, $thumb_location.'/', true);
								
								// output
								echo RSMediaGalleryHelper::applyFilters($item->params['filters'], $id, $thumb_location.'/', false, true, $res);
								
								// update the prev_filters Table
								$db->setQuery("DELETE FROM #__rsmediagallery_effects WHERE ".$db->qn('hash')."=".$db->q($hashFilter)." AND ".$db->qn('item_id')."=".$db->q($item->id));
								$db->execute();
							}
							else {
								$phpThumb->OutputThumbnail();
							}
						}
					}
				}
				else
				{
					require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/phpthumb/phpthumb.functions.php';
					
					jimport('joomla.filesystem.file');
					
					header('Content-Type: '.phpthumb_functions::ImageTypeToMIMEtype(JFile::getExt($id)));
					header('Content-Disposition: inline; filename="'.$id.'"');
					
					if($isFilter) {
						echo JFile::read($thumb_location.'/filter/'.$id);
					} else {
						echo JFile::read($thumb_location.'/'.$id);
					}
				}
				
				// remove from db
				$db->setQuery("DELETE FROM #__rsmediagallery_thumbs WHERE ".$db->qn('hash').'='.$db->q($hash).' LIMIT 1');
				$db->execute();
			}
		}
		
		jexit();
	}
	
	public function createThumbzc()
	{
		$res	 = JRequest::getCmd('resolution');
		$hash	 = md5($res.JPATH_SITE);
		$db 	 = JFactory::getDBO();
		
		$db->setQuery("SELECT * FROM #__rsmediagallery_thumbs WHERE ".$db->qn('hash').'='.$db->q($hash));		
		if ($db->loadResult())
		{
			$model	   	 			= $this->getModel('rsmediagallery');
			$mainframe 	 			= JFactory::getApplication();
			
			list($width, $height) 	= explode('x', $res);

			// get item from database
			$db->setQuery("SELECT * FROM #__rsmediagallery_items WHERE `filename`='".$db->escape($model->_getId())."'");
			if ($item = $db->loadObject())
			{
				$id 			  = $item->filename;
				$item->params	  = unserialize($item->params);
				$upload_location  = JPATH_SITE.'/components/com_rsmediagallery/assets/gallery';
				$thumb_location	  = $upload_location.'/'.$res;
				$isFilter		  = (isset($item->params['filters']) && $item->params['filters'] != '');
				
				if(isset($item->params['filters'])) {
					$hashFilter 	  = md5($res.$item->params['filters']);
					$db->setQuery("SELECT hash FROM #__rsmediagallery_effects WHERE ".$db->qn('item_id').'='.$db->q($item->id)." AND ".$db->qn('hash').'='.$db->q($hashFilter));
					$isNewFilter     = !$db->loadResult();
				}
				else $isNewFilter     = false;
				
				// cached file does not exist - must recreate it
				if (!file_exists($thumb_location.'/'.$id) || ($isFilter && (($isNewFilter && file_exists($thumb_location.'/filter/'.$id)) || !file_exists($thumb_location.'/filter/'.$id))))
				{
					require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/phpthumb/phpthumb.class.php';
					
					$phpThumb = new phpThumb();
					$phpThumb->src = $upload_location.'/original/'.$id;
					$phpThumb->w   = $width;
					$phpThumb->h   = $height;
					$phpThumb->iar = 1;
					$phpThumb->zc  = 'C';
					
					if ($phpThumb->GenerateThumbnail())
					{
						if (!is_dir($thumb_location))
						{
							jimport('joomla.filesystem.file');
							jimport('joomla.filesystem.folder');
							
							// set folder permissions
							$componentParams = JComponentHelper::getParams('com_rsmediagallery');
							$perms 			 = octdec('0'.$componentParams->get('folder_perms', '755'));
							
							if (JFolder::create($thumb_location, $perms))
							{
								$buffer = '<html><body bgcolor="#FFFFFF"></body></html>';
								JFile::write($thumb_location.'/index.html', $buffer);
							}
						}
						if ($phpThumb->RenderToFile($thumb_location.'/'.$id))
						{
							// set permissions as setup in the configuration screen
							$componentParams = JComponentHelper::getParams('com_rsmediagallery');
							$perms 			 = octdec('0'.$componentParams->get('file_perms', '644'));
							@chmod($thumb_location.'/'.$id, $perms);
							
							if($isFilter) {
								require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/helper.php';
								// copy the image with the proper effects
								RSMediaGalleryHelper::applyFilters($item->params['filters'], $id, $thumb_location.'/', true);
								
								// output
								RSMediaGalleryHelper::applyFilters($item->params['filters'], $id, $thumb_location.'/', false, true, $res);
								
								// update the prev_filters Table
								$db->setQuery("DELETE FROM #__rsmediagallery_effects WHERE ".$db->qn('hash')."=".$db->q($hashFilter)." AND ".$db->qn('item_id')."=".$db->q($item->id));
								$db->execute();
							}
							else {
								$phpThumb->OutputThumbnail();
							}
						}
					}
				}
				else
				{
					require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/phpthumb/phpthumb.functions.php';
					
					jimport('joomla.filesystem.file');
					
					header('Content-Type: '.phpthumb_functions::ImageTypeToMIMEtype(JFile::getExt($id)));
					header('Content-Disposition: inline; filename="'.$id.'"');
					
					if($item->params['filters'] != '') {
						echo JFile::read($thumb_location.'/filter/'.$id);
					} else {
						echo JFile::read($thumb_location.'/'.$id);
					}
				}
				
				// remove from db
				$db->setQuery("DELETE FROM #__rsmediagallery_thumbs WHERE ".$db->qn('hash').'='.$db->q($hash).' LIMIT 1');
				$db->execute();
			}
		}
		
		jexit();
	}
	
	public function getItems()
	{
		JRequest::setVar('view',   'rsmediagallery');
		JRequest::setVar('layout', 'items');
		JRequest::setVar('format', 'raw');
		
		parent::display();
	}
	public function getItemsAlbum()
	{
		JRequest::setVar('view',   'album');
		JRequest::setVar('layout', 'items');
		JRequest::setVar('format', 'raw');
		
		parent::display();
	}
	
	public function downloadItem()
	{
		$model = $this->getModel('rsmediagallery');
		$model->downloadItem();
	}
	
	public function hitItem()
	{
		$cid   =  JRequest::getVar('cid');
		$model = $this->getModel('rsmediagallery');
		$model->hitItem($cid);
		
		jexit();
	}
}