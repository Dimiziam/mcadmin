<?php
/**
 * Template for lists.Countries widget
 * 
 * @package MediaClub
 * @subpackage views
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
?>

<script type="text/javascript">
		$(function(){
			$('select#countries').selectmenu({
					style:'DropDown',
					class:'mediaclub',
					maxHeight: 450,
					width: 100 ,
					menuWidth:130,
					divID: 'tinyscroll1',
					nogo:'/country/', // if  false put #nogo to href, else nogo+select.text
// 					select: function(e, object){
// 				 	console.log(object.option.innerText);
// 				 	console.log(object.value);
// 					}
					});


		});



</script>


<li>
<!-- <a href="#">COUNTRIES<img src="/images/arrow_down.gif" /></a> -->
<?php echo CHtml::dropDownList('countries', 0, CHtml::listData($countries, 'id', 'name'), array('empty' => MTLang::t('countries', 'top_menu'))); ?>
</li>

<script type="text/javascript">

	//$('#tinyscroll1').tinyscrollbar();

</script>


