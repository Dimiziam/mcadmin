<?php
// Get chart translations
$translations = MTLang::instance()->getGroup('chart');

$this->breadcrumbs = array(
	$translations['charts_list'] => $this->createUrl('/chart/search/'),
	MTLang::t('add_new', 'persistence')
);

$this->menu = array(
	array('label' => $translations['charts_list'], 'url' => $this->createUrl('/chart/search/'))
);
?>

<h1><?php echo $translations['add_new_chart']; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>