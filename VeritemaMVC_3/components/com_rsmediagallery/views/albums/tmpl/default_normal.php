<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

	<?php if ($this->params->get('show_page_heading', 1)) { ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php } ?>
	
	<div id="rsmg_main" class="rsmg_fullwidth">
		<ul id="rsmg_albums">
			<?php foreach( $this->tags as $i=>$tag ) { 
				if (count($this->items[$tag])>0) {
				?>
				<li>
					<div class="rsmg_album_container">
						<a href="<?php echo $this->links[$tag]; ?>" class="rsmg_album_inner_container">
						<?php
							foreach($this->items[$tag] as $j=>$img) {
								?>
									<img src="<?php echo $img->thumb; ?>" <?php echo ($j%2==0 ? 'class="rsmg_clear_left"':'');?>/>
								<?php
							}
						?>
						</a>
						<?php if($this->params->get('show_tags_album', 1)) { ?>
							<a class="rsmg_title" href="<?php echo $this->links[$tag]; ?>">
								<?php echo $this->escape($tag);?>
							</a>
						<?php }?>
						<input type="hidden" class="rsmg_special_title" value="<?php echo RSMediaGalleryHelper::niceTag($this->escape($tag))?>"/>
					</div>
				</li>
			<?php
				}
			} ?>
		</ul>
	</div><!-- rsmg_main -->
	<span class="rsmg_clear"></span>
	<?php if ($this->params->get('show_credits', 1)) { ?>
	<div id="rsmg_footer">
		<?php echo JText::sprintf('COM_RSMEDIAGALLERY_FOOTER_CREDITS', 'http://www.rsjoomla.com/joomla-extensions/joomla-gallery.html', 'http://www.rsjoomla.com'); ?>
	</div>
	<?php } ?>