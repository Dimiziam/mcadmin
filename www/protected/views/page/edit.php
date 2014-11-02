<?php
// Get static_page translations
$translations = MTLang::instance()->getGroup('static_page');

$this->breadcrumbs = array(
	$translations['pages_list'] => $this->createUrl('/page/search/', array('type' => $type)),
	$model->id
);

$this->menu = array(
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/page/add/', array('type' => $type))),
	array('label' => $translations['pages_list'], 'url' => $this->createUrl('/page/search/', array('type' => $type)))
);
?>

<h1><?php echo $translations['edit_page']; ?>: <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', 
	array(
		'model' => $model,
		'languageModel' => $languageModel,
		'translations' => $translations,
		'language' => $language,
		'type' => $type,
	)
); ?>