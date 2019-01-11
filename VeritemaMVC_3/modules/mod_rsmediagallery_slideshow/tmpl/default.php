<?php
/**
* @version 1.0.0
* @package RSMediaGallery! 1.0.0
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<?php if ($show_controls) { ?>
<div class="rsmg_carousel_wrapper<?php echo RSMediaGalleryHelper::escape($params->get('moduleclass_sfx')); ?>" id="rsmg_carousel_wrapper<?php echo $module->id; ?>">
	<div id="rsmg_carousel_prev<?php echo $module->id; ?>"></div>
<?php } ?>
	<div class="rsmg_carousel_container" id="rsmg_carousel_container<?php echo $module->id; ?>">
	<?php if ($show_pager && ($pager_position == 'top-outside' || $pager_position == 'top-inside')) { ?>
	<div id="rsmg_carousel_pager<?php echo $module->id; ?>" class="rsmg_carousel_pager"></div>
	<?php } ?>
	<?php if ($items) { ?>
	<ul id="rsmg_slider<?php echo $module->id; ?>" class="rsmg_slider">
		<?php foreach ($items as $item) { ?>
		<li<?php echo $use_fixed_width ? ' style="width: '.$container_width.'px;"' : ''; ?>>
			<?php if ($use_url) { ?><a href="<?php echo RSMediaGalleryHelper::escape($item->url); ?>" <?php if ($open_in_new_page) { ?>target="_blank"<?php } ?> title="<?php echo RSMediaGalleryHelper::escape($item->title); ?>"><?php } ?><img src="<?php echo $use_original ? $item->full : $item->thumb; ?>" <?php if (!$use_original) { ?>width="<?php echo $item->thumb_width; ?>" height="<?php echo $item->thumb_height; ?>"<?php } elseif (is_array($item->params)) { ?>width="<?php echo $item->params['info'][0]; ?>" height="<?php echo $item->params['info'][1]; ?>"<?php } ?> class="rsmg_carousel_image<?php echo !$image_borders ? ' rsmg_carousel_image_no_borders' : ''; ?>" alt="<?php echo RSMediaGalleryHelper::escape($item->title); ?>" /><?php if ($use_url) { ?></a><?php } ?>
			<?php if ($show_title) { ?>
			<p class="rsmg_title"><?php if ($use_url) { ?><a href="<?php echo RSMediaGalleryHelper::escape($item->url); ?>" <?php if ($open_in_new_page) { ?>target="_blank"<?php } ?>><?php } ?><?php echo RSMediaGalleryHelper::escape($item->title); ?><?php if ($use_url) { ?></a><?php } ?></p>
			<?php } ?>
			<?php if ($show_description) { ?>
			<div class="rsmg_carousel_description"><?php $tmp = explode('{readmore}', $item->description); echo reset($tmp); ?></div>
			<?php } ?>
			<span class="rsmg_clear_slide"></span>
		</li>
		<?php } ?>
	</ul>
	<?php } ?>
	<?php if ($show_pager && ($pager_position == 'bottom-outside' || $pager_position == 'bottom-inside')) { ?>
	<div id="rsmg_carousel_pager<?php echo $module->id; ?>" class="rsmg_carousel_pager"></div>
	<?php } ?>
	</div><!-- rsmg_carousel_container -->
	<?php if ($show_controls) { ?>
	<div id="rsmg_carousel_next<?php echo $module->id; ?>"></div>
</div><!-- rsmg_carousel_wrapper -->
<?php } ?>