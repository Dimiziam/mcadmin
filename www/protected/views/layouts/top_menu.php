<?php
// Get top_menu translations
$translations = MTLang::instance()->getGroup('top_menu');

// Get user info
$userInfo = Yii::app()->user->getInfo();

// Prepare menu items
$items = array(
	array(
		'label' => $translations['users'], 
		'items' => array(
			array('label' => $translations['administrators'], 'url' => Yii::app()->getController()->createUrl('/admin/search/')),
			array('label' => $translations['users'], 'url' => Yii::app()->getController()->createUrl('/user/search/')),
		),
	),
	array(
		'label' => $translations['payments'], 
		'items' => array(
			array('label' => $translations['orders'], 'url' => Yii::app()->getController()->createUrl('/order/search/')),
			array('label' => $translations['payments'], 'url' => Yii::app()->getController()->createUrl('/payment/search/')),
		),
	),
	array(
		'label' => $translations['exit'] . ' (' . $userInfo['login'] . ')', 'url' => Yii::app()->getController()->createUrl('/admin/logout/')
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