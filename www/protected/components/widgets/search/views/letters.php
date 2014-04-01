<?php
/**
 * Template fro search.Letters widget
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
?>
<div class="letters_catalog">
	<div class="letters_catalog_list">
		<ul>
			<?php foreach ($letters as $letter => $id) { ?>
				<li>
					<a href="<?php echo Yii::app()->createUrl('/search/letters/', array('letter' => $letter)) ?>"><?php echo $letter; ?></a>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>
