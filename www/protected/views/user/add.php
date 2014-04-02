<?php
// Get user translations
$translations = MTLang::instance()->getGroup('user');

$this->breadcrumbs = array(
	$translations['users_list'] => $this->createUrl('/user/search/'),
	MTLang::t('add_new', 'persistence')
);

$this->menu = array(
	array('label' => $translations['users_list'], 'url' => $this->createUrl('/user/search/'))
);
?>

<h1><?php echo $translations['add_new_user']; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>