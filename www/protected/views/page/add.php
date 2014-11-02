<?php
// Get static_page translations
$translations = MTLang::instance()->getGroup('static_page');

$this->breadcrumbs = array(
	$translations['pages_list'] => $this->createUrl('/page/search/', array('type' => $type)),
	MTLang::t('add_new', 'persistence')
);

$this->menu = array(
	array('label' => $translations['pages_list'], 'url' => $this->createUrl('/page/search/', array('type' => $type)))
);
?>

<h1><?php echo $translations['add_new_page']; ?></h1>

<?php echo $this->renderPartial('_form', 
	array(
		'model' => $model, 
		'languageModel' => $languageModel, 
		'translations' => $translations,
		'language' => $language,
		'type' => $type,
	)
); ?>