<?php
// Get chart translations
$translations = MTLang::instance()->getGroup('chart');

$this->breadcrumbs = array(
	$translations['charts_list'] => $this->createUrl('/chart/search/'),
	$model->title
);

$this->menu = array(
	array('label' => $translations['charts_list'], 'url' => $this->createUrl('/chart/search/')),
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/chart/add/')),
	array('label' => $translations['items_link_title'], 'url' => $this->createUrl('/chart/items/', array('id' => $model->id))),
);
?>

<h1><?php echo $translations['edit_chart']; ?>: <?php echo $model->title; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>