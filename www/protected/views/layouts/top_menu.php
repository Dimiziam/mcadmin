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
			array('label' => $translations['affiliate'], 'url' => Yii::app()->getController()->createUrl('/user/affiliate/')),
		),
	),
	array(
		'label' => $translations['payments'], 
		'items' => array(
			array('label' => $translations['orders'], 'url' => Yii::app()->getController()->createUrl('/order/search/')),
			array('label' => $translations['payments'], 'url' => Yii::app()->getController()->createUrl('/transaction/search/')),
		),
	),
	array(
		'label' => $translations['content'], 
		'items' => array(
			array('label' => $translations['charts'], 'url' => Yii::app()->getController()->createUrl('/chart/search/')),
			array('label' => $translations['pages'], 'url' => Yii::app()->getController()->createUrl('/page/search/', array('type' => 'page'))),
			array('label' => $translations['news'], 'url' => Yii::app()->getController()->createUrl('/page/search/', array('type' => 'news'))),
		),
	),
	array(
		'label' => $translations['report'],
		'items' => array(
			array('label' => $translations['osa_report'], 'url' => Yii::app()->getController()->createUrl('/report/osa/')),
			array('label' => $translations['paypal_report'], 'url' => Yii::app()->getController()->createUrl('/report/paypal/'))
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