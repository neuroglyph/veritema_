<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');
	if ($this->responsive) {
		echo $this->loadTemplate('responsive');
	}
	else {
		echo $this->loadTemplate('normal');
	}