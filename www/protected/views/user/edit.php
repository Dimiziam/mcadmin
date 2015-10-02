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


$this->menu = array(
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/user/add/')),
	array('label' => $translations['users_list'], 'url' => $this->createUrl('/user/search/'))
);
?>

<h1><?php echo $translations['edit_user']; ?>: <?php echo $model->email; ?></h1>

<h3><?php echo MTLang::instance()->t('country_code','transaction').': '.$model->country_code?><br>
<?php echo MTLang::instance()->t('amount','transaction');?>: <?php echo $model->amount?> </h3>



<?php echo MTLang::instance()->t('billing_total_succsessful','transaction');?><br>
<?php
if (!empty($payments)) {
	foreach ($payments as $item) {
		echo MTLang::instance()->t('billing_' . $item['billing'],'transaction') . ': '.$item['total'];
		echo "<br>";
	}
}
?>
	<br>
<?php echo $translations['http_referer'] . ' <b>' . $model->http_referer; ?></b>
<br>
<br>
<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>