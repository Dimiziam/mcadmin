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

 <a href="<?php echo $this->createUrl('/order/search/', array('SearchOrderForm[user_id]' => $model->id)); ?>"
                       title="<?php echo $translations['orders_link_title']; ?>">
                      <?php echo $translations['order_list']; ?>
</a>

	<br><br>

<?php if (!empty($model->http_referer))  {?>
	<?php echo $translations['http_referer'] . ' <b><a href="'.$model->http_referer.'" target="_blank">' . substr($model->http_referer,0,70) ?></a></b><br>
<?php  }?>
<?php echo $translations['registered'] . ' <b>' . $model->created; ?></b><br>

<?php if  (!(empty($model->last_login_time))) {?>
    <?php echo $translations['last_login'] . ' <b>' . date("Y-m-d H:i:s",$model->last_login_time); ?></b>
<?php }?>
<br>
<br>
<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>