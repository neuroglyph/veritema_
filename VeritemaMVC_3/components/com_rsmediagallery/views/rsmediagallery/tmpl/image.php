<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

	<?php if ($this->params->get('show_page_heading', 1)) { ?>
		<h1><?php echo $this->escape($this->item->title); ?></h1>
	<?php } ?>
	<div id="rsmg_main">
		<p>
		<?php if ($this->adjacent->prev) { ?><a href="<?php echo RSMediaGalleryRoute::_('index.php?option=com_rsmediagallery&view=rsmediagallery&layout=image&id='.$this->adjacent->prev->filepart.'&ext='.$this->adjacent->prev->fileext); ?>" class="rsmg_float_left"><?php echo JText::_('COM_RSMEDIAGALLERY_PREVIOUS'); ?></a><?php } ?>
		<?php if ($this->adjacent->next) { ?><a href="<?php echo RSMediaGalleryRoute::_('index.php?option=com_rsmediagallery&view=rsmediagallery&layout=image&id='.$this->adjacent->next->filepart.'&ext='.$this->adjacent->next->fileext); ?>" class="rsmg_float_right"><?php echo JText::_('COM_RSMEDIAGALLERY_NEXT'); ?></a><?php } ?>
		</p>
		<span class="rsmg_clear"></span>
		<noscript>
		<div id="rsmg_force_show">
		</noscript>
    	<div id="rsmg_image_container" class="rsmg_hidden_from_view">
			<div id="rsmg_thumb_container">
			<img src="<?php echo $this->item->src; ?>" alt="<?php echo $this->escape($this->item->title); ?>" title="<?php echo $this->escape($this->item->title); ?>" />
			<div class="rsmg_back"><a href="<?php echo RSMediaGalleryRoute::_('index.php?option=com_rsmediagallery&view=rsmediagallery'); ?>"><?php echo JText::_('COM_RSMEDIAGALLERY_BACK_TO_GALLERY'); ?></a></div>
			<?php if ($this->params->get('show_title_detail', 1)) { ?>
            <h2 class="rsmg_title"><?php echo $this->escape($this->item->title); ?></h2>
			<?php } else { ?>
			<span class="rsmg_spacer"></span>
			<?php } ?>
				<?php if ($this->params->get('show_description_detail', 1)) { ?>
				<?php echo $this->item->description; ?>
				<?php } ?>
				<span class="rsmg_clear"></span>
				<?php if ($this->params->get('download_original', 1)) { ?>
				<div class="rsmg_download rsmg_toolbox"><a href="<?php echo RSMediaGalleryRoute::_('index.php?option=com_rsmediagallery&task=downloaditem&id='.$this->item->filepart.'&ext='.$this->item->fileext); ?>"><?php echo JText::_('COM_RSMEDIAGALLERY_DOWNLOAD'); ?></a></div>
				<?php } ?>
				<?php if ($this->params->get('show_hits', 1)) { ?>
				<div class="rsmg_views rsmg_toolbox"><?php echo JText::sprintf($this->item->hits == 1 ? 'COM_RSMEDIAGALLERY_HIT' : 'COM_RSMEDIAGALLERY_HITS', $this->item->hits); ?></div><!-- rsmg_views -->
				<?php } ?>
				<?php if ($this->params->get('show_created', 1)) { ?>
				<div class="rsmg_calendar rsmg_toolbox"><?php echo JText::sprintf('COM_RSMEDIAGALLERY_CREATED', $this->escape($this->item->created)); ?></div>
				<?php } ?>
				<?php if ($this->params->get('show_modified', 1)) { ?>
				<div class="rsmg_calendar rsmg_toolbox"><?php echo JText::sprintf('COM_RSMEDIAGALLERY_MODIFIED', $this->escape($this->item->modified)); ?></div>
				<?php } ?>
				<span class="rsmg_clear"></span>
			</div>
			<?php if ($this->params->get('show_tags', 1)) { ?>
			<p class="rsmg_tags"><?php echo JText::_('COM_RSMEDIAGALLERY_TAGS'); ?>: <strong><?php echo $this->escape($this->item->tags); ?></strong></p>
			<?php } ?>
         </div><!-- rsmg_image_container -->
		 <noscript>
		 <div>
		 </noscript>
		 <div id="rsmg_loader_container" style="display: none;">
			<img src="<?php echo JURI::root(true); ?>/components/com_rsmediagallery/assets/images/loader.gif" alt="<?php echo JText::_('COM_RSMEDIAGALLERY_LOADING'); ?>" />
		 </div>
	</div><!-- rsmg_main -->
	<span class="rsmg_clear"></span>