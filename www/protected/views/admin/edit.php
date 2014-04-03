<?php
// Get admin translations
$translations = MTLang::instance()->getGroup('admin');

$this->breadcrumbs = array(
	$translations['admins_list'] => $this->createUrl('/admin/search/'),
	$model->login
);

$this->menu = array(
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/admin/add/')),
	array('label' => $translations['admins_list'], 'url' => $this->createUrl('/admin/search/'))
);
?>

<h1><?php echo $translations['edit_admin']; ?>: <?php echo $model->login; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>