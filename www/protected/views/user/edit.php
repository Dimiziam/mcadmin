<?php
// Get user translations
$translations = MTLang::instance()->getGroup('user');

$this->breadcrumbs = array(
	$translations['users_list'] => $this->createUrl('/user/search/'),
	$model->email
);

$this->menu = array(
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/user/add/')),
	array('label' => $translations['users_list'], 'url' => $this->createUrl('/user/search/'))
);
?>

<h1><?php echo $translations['edit_user']; ?>: <?php echo $model->email; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>