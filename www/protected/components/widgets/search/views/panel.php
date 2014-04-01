<?php
/**
 * Template fro search.Letters widget
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */

echo CHtml::form('/search/', 'GET',array('id' => 'searchform') );

// Prepare dropdown alement
$dropDownGenre = CHtml::dropDownList(
	'genre_search', 0, CHtml::listData($genres, 'id', 'name'), array('empty' => 'All genres')
);

?>


<script type="text/javascript">
	$(function(){
		$('select#genre_search').selectmenu({
			style:'DropDown',
			class:'mediaclub',
			maxHeight: 450,
			width: 100 ,
			menuWidth:130
		});
	})


</script>

<!-- search -->
<div class="search_header"> Search: <?php echo $dropDownGenre; ?> </div>

<div class="search_field">
	<input type="text" id="search_auto" name="search_auto"
		class="search_input" value=""\>
	<button name="search_sumit" class="buttons" type="submit" />
	<?php echo Yii::t('intf','Search');?>
	</button>
	<a href="#" class="adv_search">Advanced Search</a>
</div>

<?php echo CHtml::hiddenField('id_singer','0', array('style'=>'display: none;')); ?>

<!-- //search -->
<?php echo CHtml::endForm()?>

<?php
// <a href="#search" class="underline_dotted">All genres<img
// src="/images/arrow_down_black.gif" /></a>
?>