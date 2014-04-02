<?php
// Get user info
$userInfo = Yii::app()->user->getInfo();

// Prepare menu items
$items = array(
	array(
		'label' => 'Пользователи', 
		'items' => array(
			array('label' => 'Администраторы', 'url' => Yii::app()->getController()->createUrl('/admin/search/')),
			array('label' => 'Пользователи', 'url' => Yii::app()->getController()->createUrl('/user/search/')),
		),
	),
	array(
		'label' => 'Выход (' . $userInfo['login'] . ')', 'url' => Yii::app()->getController()->createUrl('/admin/logout/')
	)
);

function buildMenu($items)
{
	foreach ($items as $item) {
		// Check is item visible
		if (isset($item['visible']) && $item['visible'] === false) {
			continue;
		}
		
		echo '<li ' . (!empty($item['items']) ? 'class="current"' : '') . '>';
		echo '<a href="' . (!empty($item['url']) ? $item['url'] : 'JavaScript: void(0);') . '">' . $item['label'] . '</a>';
		if (!empty($item['items'])) {
			echo '<ul>';
			buildMenu($item['items']);
			echo '</ul>';
		}
		echo '</li>';
	}
}
?>

<ul class="sf-menu">
	<?php buildMenu($items); ?>
</ul>


<script type="text/javascript">

// initialise plugins
jQuery(function(){
	$('ul.sf-menu').superfish();
});

</script>