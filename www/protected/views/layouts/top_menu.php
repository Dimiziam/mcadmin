<?php
// Get user info
$userInfo = Yii::app()->user->getInfo();

// Prepare menu items
$items = array(
	array(
		'label' => 'Пользователи', 
		'items' => array(
			array('label' => 'Администраторы', 'url' => './index.php?r=user&type=admin'),
			array('label' => 'Модераторы', 'url' => './index.php?r=user&type=moderator'),
			array(
				'label' => 'Игроки',
				'items' => array(
					array('label' => 'Список', 'url' => './index.php?r=player'),
					array('label' => 'Параметры', 'url' => './index.php?r=parameter'),
					array('label' => 'Свойства', 'url' => './index.php?r=property/groupIndex&groupId=0'),
					array('label' => 'Виды энергии', 'url' => './index.php?r=energy'),
					array('label' => 'Расы', 'url' => './index.php?r=race'),
					array('label' => 'Образы', 'url' => './index.php?r=playerImage/groupIndex&groupId=0'),
				)
			),
			array('label' => 'NPC', 'url' => './index.php?r=npc/groupIndex&groupId=0'),
		),
	),
	array(
		'label' => 'Выход (' . $userInfo['login'] . ')', 'url' => '/admin/logout/'
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