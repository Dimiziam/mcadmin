<?php
// Get admin translations
$translations = MTLang::instance()->getGroup('admin');

$this->breadcrumbs = array(
	$translations['admins_list'] => $this->createUrl('/admin/search/'),
	MTLang::t('add_new', 'persistence')
);

$this->menu = array(
	array('label' => $translations['admins_list'], 'url' => $this->createUrl('/admin/search/'))
);
?>

<h1><?php echo $translations['add_new_admin']; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>