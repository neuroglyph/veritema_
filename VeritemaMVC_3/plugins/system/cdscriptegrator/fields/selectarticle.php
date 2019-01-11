<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 2.5
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla 
 * @subpackage	System
 * @category	Plugin
 * @version		2.5.x.2.2.9
 * @copyright	Copyright (C) 2007 - 2012 Great Joomla!, http://www.greatjoomla.com
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL 3
 * 
 * This file is part of Great Joomla! extension.   
 * This extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldSelectArticle extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public	$type 		= 'SelectArticle';
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		jimport('joomla.plugin.helper');
		
		$error_msg_tmpl = '<div style="float: left; border: 1px solid left; width: 250px; font-weight: bold; color: red; margin: 5px 0;">%error%</div>';
		
		if (!JPluginHelper::isEnabled('system', 'cdscriptegrator'))
		{
			echo str_replace('%error%', 'Please install and enable Core Design Scriptegrator plugin.', $error_msg_tmpl);
			return false;
		}
		
		$attr = '';
		
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		if ((string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
		
		// Build the script.
		$script = array();
		
		// UI theme styling
		$uitheme = ($this->element['uitheme'] ? $this->element['uitheme'] : 'smoothness');
		
		$JScriptegrator = JScriptegrator::getInstance('2.5.x.2.1.8');
		$JScriptegrator->importLibrary(
		array(
			'phpjs',
			'jQuery',
			'jQueryUI' => array(
				'uitheme' => (string)$uitheme
			)
		));
		
		if ($error = $JScriptegrator->getError())
		{
			echo str_replace('%error%', $error, $error_msg_tmpl);
			return false;
		}
		
		// Add the script to the document head.
		$document = JFactory::getDocument();
		
		$document->addScript($JScriptegrator->folderPath() . '/fields/' . strtolower($this->type) . '/js/' . strtolower($this->type) . '.js');
		$document->addStyleSheet($JScriptegrator->folderPath() . '/fields/' . strtolower($this->type) . '/css/' . strtolower($this->type) . '.css');
		
		// Script function
		$script[] = "
		jQuery(document).ready(function($){
			$('#" . $this->id . "')." . strtolower($this->type) . "({
				scriptURL : '" . $JScriptegrator->folderPath() . '/fields/' . strtolower($this->type) . "/application.php" . "',
			});
		});
		";
		
		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT title' .
			' FROM #__content' .
			' WHERE id = '.(int) $this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg())
		{
			JError::raiseWarning(500, $error);
		}
		
		$document->addScriptDeclaration(implode("\n", $script));
		
		$html = '';
		
		$html .= '<div class="fltlft ' . $uitheme . '">';
		$html .= '<input type="text" name="' . $this->name . '_selectarticle" id="' . $this->id . '"' .
				' value="' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '"' .
				trim($attr).' style="width: 200px;" />';
		$html .= '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '_selectarticle_hidden" value="' . $value . '" />';
		$html .= '</div>';
				
		return $html;
	}
}
?>