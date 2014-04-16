<?php
// Get chart translations
$translations = MTLang::instance()->getGroup('chart');

$this->breadcrumbs = array(
	$translations['charts_list'] => $this->createUrl('/chart/search/'),
	$chart->title => $this->createUrl('/chart/edit/', array('id' => $chart->id)),
	$translations['chart_items_list'] => $this->createUrl('/chart/items/', array('id' => $chart->id)),
	$translations['add_new_chart_item']
);

$this->menu = array(
	array('label' => $translations['chart_items_list'], 'url' => $this->createUrl('/chart/items/', array('id' => $chart->id)))
);
?>

<h1><?php echo $translations['add_new_chart_item']; ?></h1>

<?php echo $this->renderPartial('_form_item', array('model' => $model, 'translations' => $translations)); ?>