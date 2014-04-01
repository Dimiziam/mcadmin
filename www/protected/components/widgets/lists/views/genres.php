<?php
/**
 * Template for lists.Genres widget
 * 
 * @package MediaClub
 * @subpackage views
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
?>

<li>
<!-- <li><a href="#">GENRE<img src="/images/arrow_down.gif" /></a></li> -->
<?php echo CHtml::dropDownList('genres', 0, CHtml::listData($genres, 'id', 'name'), array('empty' => MTLang::t('genres', 'top_menu'))); ?>
</li>


