<?php
// Get user translations
$translations = MTLang::instance()->getGroup('user');


if ($model->f=='affiliate') {

	$this->breadcrumbs = array(
		$translations['affiliate_h2'] => $this->createUrl('/user/affiliate/') ,
		$model->email
	);
}
else {
	$this->breadcrumbs = array(
		$translations['users_list'] => $this->createUrl('/user/search/'),
		$model->email
	);

}

pc::debug($model->f);
$this->menu = array(
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/user/add/')),
	array('label' => $translations['users_list'], 'url' => $this->createUrl('/user/search/'))
);
?>

<h1><?php echo $translations['edit_user']; ?>: <?php echo $model->email; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>