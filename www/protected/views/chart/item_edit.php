<?php
// Get chart translations
$translations = MTLang::instance()->getGroup('chart');

$this->breadcrumbs = array(
	$translations['charts_list'] => $this->createUrl('/chart/search/'),
	$chart->title => $this->createUrl('/chart/edit/', array('id' => $chart->id)),
	$translations['chart_items_list'] => $this->createUrl('/chart/items/', array('id' => $chart->id)),
	$model->id
);

//$this->menu = array(
//	array('label' => $translations['chart_items_list'], 'url' => $this->createUrl('/chart/items/', array('id' => $chart->id)))
//);

$this->menu = array(
	array('label' => $translations['charts_list'], 'url' => $this->createUrl('/chart/items/', array('id' => $chart->id))),
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/chart/add/', array('sourceId' => $chart->id))),
);
?>

<h1><?php echo $translations['edit_chart_item']; ?>: <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form_item', array('model' => $model, 'translations' => $translations)); ?>