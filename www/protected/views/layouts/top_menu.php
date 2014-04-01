<?php
// Prepare menu items
$items = array(
	array(
		'label' => 'Система',
		'visible' => Yii::app()->user->getState('type') == 'admin', 
		'items' => array(
			array('label' => 'Сводная статистика', 'url' => './index.php?r=statistic/common'),
			array('label' => 'Хранилища', 'url' => './index.php?r=storage'),
			array('label' => 'Боевые сервера', 'url' => './index.php?r=battleServer'),
			array('label' => 'Смайлы', 'url' => './index.php?r=smile/groupIndex&groupId=0'),
			array('label' => 'Валюты', 'url' => './index.php?r=currency'),
		)
	),
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
		'visible' => Yii::app()->user->getState('type') == 'admin'
	),
	array(
		'label' => 'Игровой мир', 
		'items' => array(
			array('label' => 'Карты', 'url' => './index.php?r=map'),
			array('label' => 'Локации', 'url' => './index.php?r=location/groupIndex&groupId=0'),
			array('label' => 'Настройки ячеек', 'url' => './index.php?r=ceilSetting'),
			array(
				'label' => 'Объекты карты',
				'items' => array(
					array('label' => 'Список', 'url' => './index.php?r=object/groupIndex&groupId=0'),
					array('label' => 'Виды объектов', 'url' => './index.php?r=objectType/typeIndex&typeId=0'),
				)
			),
			array('label' => 'Изображения', 'url' => './index.php?r=mapImage/entityIndex&entityId=0&groupId=0'),
		),
		'visible' => Yii::app()->user->getState('type') == 'admin'
	),
	array(
		'label' => 'Профессии', 
		'items' => array(
			array('label' => 'Список', 'url' => './index.php?r=profession'),
			array('label' => 'Ресурсы', 'url' => './index.php?r=resource/groupIndex&groupId=0'),
			array('label' => 'Материалы', 'url' => './index.php?r=material/groupIndex&groupId=0'),
			array('label' => 'Ремесла', 'url' => './index.php?r=craft/groupIndex&groupId=0'),
		),
		'visible' => Yii::app()->user->getState('type') == 'admin'
	),
	array(
		'label' => 'Предметы', 
		'items' => array(
			array('label' => 'Чертежи/Рецепты', 'url' => './index.php?r=drawing/groupIndex&groupId=0'),
			array('label' => 'Предназначения предметов', 'url' => './index.php?r=drawingPurpose/index'),
			array('label' => 'Эффекты', 'url' => './index.php?r=effect/groupIndex&groupId=0'),
		),
		'visible' => Yii::app()->user->getState('type') == 'admin'
	),
	array(
		'label' => 'Магия', 
		'items' => array(
			array('label' => 'Школы', 'url' => './index.php?r=magicType/index'),
			array('label' => 'Руны', 'url' => './index.php?r=rune/groupIndex&groupId=0'),
			array('label' => 'Заклинания', 'url' => './index.php?r=spell/groupIndex&groupId=0'),
			array('label' => 'Свитки', 'url' => './index.php?r=magicScroll/index'),
			array('label' => 'Сферы', 'url' => './index.php?r=magicSphere/index'),
		),
		'visible' => Yii::app()->user->getState('type') == 'admin'
	),
	array(
		'label' => 'Выход (' . Yii::app()->user->login . ')', 'url' => './index.php?r=user/logout'
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