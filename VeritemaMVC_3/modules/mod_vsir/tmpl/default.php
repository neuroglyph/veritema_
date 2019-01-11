<?php

/*------------------------------------------------------------------------

# mod_vsir - Very Simple Image Rotator

# ------------------------------------------------------------------------

# author    Joomla!Vargas

# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://joomla.vargas.co.cr

# Technical Support:  Forum - http://joomla.vargas.co.cr/forum

-------------------------------------------------------------------------*/



// no direct access

defined('_JEXEC') or die;



JHtml::_('behavior.framework', true);



$doc = JFactory::getDocument();



$doc->addStyleDeclaration(modVsir::buildCSS( $params, $mod_vsig_id ));



$doc->addScript(JURI::root(true).'/modules/mod_vsir/js/vsir.js');



$doc->addScriptDeclaration("window.addEvent('domready',function(){" .

	"var vsir_" . $mod_vsig_id . " = new Vsir('.vsig_slide_" . $mod_vsig_id . "',{" .

		"slideInterval:" . $params->get('delay', 2000) . "," .

		"transitionDuration:" . $params->get('trans', 3000) . "" .

	"});" .

"});" .	

"");

?>

    <div id="vsir_<?php echo $mod_vsig_id; ?>"><?php foreach ( $images as $image ) : ?>

    

        <div class="vsig_slide_<?php echo $mod_vsig_id; ?>" ><?php echo $image; ?></div><?php endforeach; ?>

        

    </div>

