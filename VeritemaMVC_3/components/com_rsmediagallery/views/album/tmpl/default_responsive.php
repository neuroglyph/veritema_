<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');
$spanType = floor(12 / $this->itemsrow);
?>

	<?php if ($this->params->get('show_page_heading', 1)) { ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php } ?>
	
	<div id="rsmg_main" class="rsmg_fullwidth">
		<?php if ($this->prev) { ?>
		<a href="<?php echo RSMediaGalleryRoute::_('index.php?option=com_rsmediagallery&view=rsmediagallery'.($this->limitstart - $this->limit > 0 ? '&limitstart='.($this->limitstart - $this->limit) : '')); ?>" id="rsmg_prev_page" class="rsmg_big_button"><?php echo JText::_('COM_RSMEDIAGALLERY_PREV_PAGE'); ?></a>
		<?php } ?>
		
		<?php if ($this->items) { ?>
		<ul class="row-fluid" id="rsmg_gallery">
			<?php foreach ($this->items as $i => $item) { 
				$margin_class = '';
				if ($i % $this->itemsrow == 0) {
					$margin_class = 'margin_left_none';
					
				}
			?>
				<li class="span<?php echo $spanType.' '.$margin_class; ?>">
				<div class="rsmg_item_container">
					<div class="rsmg_image" >
						<a class="rsmg_lightbox" <?php if ($this->params->get('open_in_new_page', 0)) { ?>target="_blank"<?php } ?> href="<?php echo $item->href; ?>" rel="{'link': '<?php echo addslashes($item->full); ?>', 'title': '#rsmg_item_<?php echo $i; ?>', 'id': '<?php echo $item->id; ?>', 'group' : 'all'}" title="">
							<img src="<?php echo $item->thumb; ?>" alt="<?php echo $this->escape($item->title); ?>" />
						</a>
					</div>
					<?php if ($this->params->get('show_title_list', 1) || $this->params->get('show_description_list', 1)) {?>
						<div class="rsmg_image_details">
							<?php if ($this->params->get('show_title_list', 1)) { ?>
								<a <?php if ($this->params->get('open_in_new_page', 0)) { ?>target="_blank"<?php } ?> href="<?php echo $item->href; ?>" class="rsmg_title"><?php echo $this->escape($item->title); ?></a>
							<?php } ?>
							<?php if ($this->params->get('show_description_list', 1)) { ?>
								<span class="rsmg_item_description"><?php echo $item->description; ?></span>
							<?php } ?>
						</div>
					<?php } ?>
					<div id="rsmg_item_<?php echo $i; ?>" class="rsmg_item_rest" style="display: none;">
						<?php if ($this->params->get('show_title_detail', 1)) { ?>
						<h2 class="rsmg_title"><?php echo $this->escape($item->title); ?></h2>
						<?php } ?>
						<?php if ($this->params->get('show_description_detail', 1)) { ?>
							<?php echo $item->full_description; ?>
						<?php } ?>
						<span class="rsmg_clear"></span>
						<?php if ($this->params->get('download_original', 1)) { ?>
						<div class="rsmg_download rsmg_toolbox"><a href="<?php echo RSMediaGalleryRoute::_('index.php?option=com_rsmediagallery&task=downloaditem&id='.$item->filepart.'&ext='.$item->fileext); ?>"><?php echo JText::_('COM_RSMEDIAGALLERY_DOWNLOAD'); ?></a></div>
						<?php } ?>
						<?php if ($this->params->get('show_hits', 1)) { ?>
						<div class="rsmg_views rsmg_toolbox"><?php echo JText::sprintf($item->hits == 1 ? 'COM_RSMEDIAGALLERY_HIT' : 'COM_RSMEDIAGALLERY_HITS', $item->hits); ?></div>
						<?php } ?>
						<?php if ($this->params->get('show_created', 1)) { ?>
						<div class="rsmg_calendar rsmg_toolbox"><?php echo JText::sprintf('COM_RSMEDIAGALLERY_CREATED', $this->escape($item->created)); ?></div>
						<?php } ?>
						<?php if ($this->params->get('show_modified', 1)) { ?>
						<div class="rsmg_calendar rsmg_toolbox"><?php echo JText::sprintf('COM_RSMEDIAGALLERY_MODIFIED', $this->escape($item->modified)); ?></div>
						<?php } ?>
						<span class="rsmg_clear"></span>
						<?php if ($this->params->get('show_tags', 1)) { ?>
						<p class="rsmg_tags"><?php echo JText::_('COM_RSMEDIAGALLERY_TAGS'); ?>: <strong><?php echo $this->escape($item->tags); ?></strong></p>
						<?php } ?>
					</div>
				</div>
				</li>
			<?php } ?>
		</ul>
		<!-- rsmg_gallery -->
		<?php } ?>
		
		<?php if ($this->more) { ?>
		<a href="<?php echo RSMediaGalleryRoute::_('index.php?option=com_rsmediagallery&view=album&tag='.urlencode($this->tag).'&limitstart='.($this->limitstart + $this->limit)); ?>" rel="<?php echo $this->total - $this->limitstart; ?>" id="rsmg_load_more" class="rsmg_big_button rsmg_album_layout"><?php echo JText::_('COM_RSMEDIAGALLERY_NEXT_PAGE'); ?></a>
		<input type="hidden" name="tag" id="selectedTag" value="<?php echo $this->tag;?>" />
		<?php } ?>
		
		<input type="hidden" name="Itemid" id="rsmg_itemid" value="<?php echo $this->itemid; ?>" />
		<input type="hidden" id="rsmg_original_limitstart" value="<?php echo $this->limitstart; ?>" />
	</div><!-- rsmg_main -->
	<span class="rsmg_clear"></span>
	<?php if ($this->params->get('show_credits', 1)) { ?>
	<div id="rsmg_footer">
		<?php echo JText::sprintf('COM_RSMEDIAGALLERY_FOOTER_CREDITS', 'http://www.rsjoomla.com/joomla-extensions/joomla-gallery.html', 'http://www.rsjoomla.com'); ?>
	</div>
	<?php } ?>