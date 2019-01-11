<?php
/**
* @title		Simple image gallery module
* @website		http://www.joomshaper.com
* @copyright	Copyright (C) 2010 - 2012 JoomShaper.com. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="sp-sig<?php echo $uniqid ?>" class="sp-sig">
<?php foreach($list as $item) { ?>
	<a href="<?php echo $item->image ?>" rel="lightbox-atomium" title="<?php echo $item->title ?>">
		<img class="sp_simple_gallery" src="<?php echo $item->thumb ?>" alt="<?php echo $item->title ?>" />
	</a>
<?php } ?>
</div>
<?php if ($usefade) { ?>
<script type="text/javascript">
	window.addEvent("domready",function(){
		$$("div#sp-sig<?php echo $uniqid ?> img").each(function(e){
		  e.setOpacity(<?php echo $normal_opacity ?>);
		  e.addEvent("mouseover", function(){e.fade(<?php echo $hover_opacity ?>);});
		  e.addEvent("mouseout", function(){e.fade(<?php echo $normal_opacity ?>);});			  
		});
	});
</script>
<?php } ?>