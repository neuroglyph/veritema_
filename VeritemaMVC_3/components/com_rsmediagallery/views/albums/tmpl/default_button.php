<?php
/**
* @package RSMediaGallery!
* @copyright (C) 2011-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die('Restricted access');
//echo '<pre>';
//var_dump($this->items);
//echo '</pre>';

?>

	<ul id="rsmg_albums">
		<?php foreach( $this->tags as $i=>$tag ) { ?>
			<li class="rsmg_album_buttons">
				<a class="rsmg_title" href="<?php echo $this->links[$tag]; ?>">
				<?php echo $this->escape($tag);?>
				</a>
			</li>
		<?php } ?>
	</ul>