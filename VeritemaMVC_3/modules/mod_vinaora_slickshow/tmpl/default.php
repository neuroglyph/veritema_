<?php
/**
 * @version		$Id: default.php 2012-03-19 vinaora $
 * @package		VINAORA SLICK SLIDESHOW
 * @subpackage	mod_vinaora_slickshow
 * @copyright	Copyright (C) 2010 - 2012 VINAORA. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @website		http://vinaora.com
 * @twitter		http://twitter.com/vinaora
 * @facebook	http://facebook.com/vinaora
 */

// no direct access
defined('_JEXEC') or die;
?>

<!-- BEGIN: VINAORA SLICK SLIDESHOW -->
<!-- Website http://vinaora.com -->
<style>
#vslickshow<?php echo $module_id; ?>{
	width:<?php echo $width; ?>px;
	height:<?php echo $height; ?>px;
}
<?php if($border_width){ ?>
#vslickshow<?php echo $module_id; ?> {
	border-color:<?php echo $border_color; ?>;
	border-style:<?php echo $border_style; ?>;
	border-width:<?php echo $border_width; ?>px;
}

<?php if($border_rounded){ ?>
#vslickshow<?php echo $module_id; ?> {
	-moz-border-radius: 8px 8px 8px 8px;
	-webkit-border-radius: 8px 8px 8px 8px;
	border-radius: 8px 8px 8px 8px;
}
<?php } ?>
<?php if($border_shadow){ ?>
#vslickshow<?php echo $module_id; ?> {
	-webkit-box-shadow: 0px 1px 5px 0px #4a4a4a;
	-moz-box-shadow: 0px 1px 5px 0px #4a4a4a;
	box-shadow: 0px 1px 5px 0px #4a4a4a;
}
<?php } ?>
<?php } ?>
</style>
<script type="text/javascript">

	// JAVASCRIPT VARS
	// cache buster
	var cacheBuster = "?t=" + Date.parse(new Date());

	// stage dimensions
	var stageW = <?php echo $width; ?>;//"100%";
	var stageH = <?php echo $height; ?>;//"100%";

	// ATTRIBUTES
	var attributes = {};
	attributes.id = '<?php echo $container; ?>';
	attributes.name = attributes.id;

	// PARAMS
	var params = {};
	params.bgcolor = "<?php echo $backgroundColor; ?>";
	params.wmode 	= "<?php echo $flash_wmode; ?>";

	//params.menu = "false";
	//params.scale = 'noScale';
	//params.wmode = "opaque";
	//params.allowfullscreen = "true";
	//params.allowScriptAccess = "always";

	/* FLASH VARS */
	var flashvars = {};

	/// if commented / delete these lines, the component will take the stage dimensions defined
	/// above in "JAVASCRIPT SECTIONS" section or those defined in the settings xml
	flashvars.componentWidth = stageW;
	flashvars.componentHeight = stageH;

	/// path to the content folder(where the xml files, images or video are nested)
	/// if you want to use absolute paths(like "http://domain.com/images/....") then leave it empty("")
	flashvars.pathToFiles = "";

	// path to content XML
	flashvars.xmlPath = "<?php echo $config_path; ?>"

	/** EMBED THE SWF**/
	swfobject.embedSWF("<?php echo $stage_path; ?>"+cacheBuster, attributes.id, stageW, stageH, "<?php echo $flash_version; ?>", "<?php echo $expressInstall_path; ?>", flashvars, params);

</script>
<div id="vslickshow<?php echo $module_id; ?>" class="vslickshow<?php echo $moduleclass_sfx; ?>">
	<!-- this div will be overwritten by SWF object -->
	<div id="<?php echo $container; ?>">
		<p>In order to view this object you need Flash Player 9+ support!</p>
		<a href="http://www.adobe.com/go/getflashplayer">
			<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"/>
		</a>
		<br />
		<a href="http://vinaora.com/">Joomla! Slideshow</a>
	</div>
</div>
<!-- Website http://vinaora.com -->
<!-- END: VINAORA SLICK SLIDESHOW  -->
