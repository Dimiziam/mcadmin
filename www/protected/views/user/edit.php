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
	array('label' => $translations['users_list'], 'url' => $this->createUrl('/user/search/')),
	array('label' => $translations['user_payments'], 'url' => $this->createUrl('/transaction/search/',
		array('SearchTransactionForm[user_id]' => $model->id,
			'SearchTransactionForm[status]' => '',
			'SearchTransactionForm[billing]' => ''))
	),
	array('label' => $translations['order_list'],
		'url' => $this->createUrl('/order/search/', array('SearchOrderForm[user_id]' => $model->id))
	),
	array('label' => $translations['change_balance_link_title'],
		'url' => $this->createUrl('/transaction/add/', array('userId' =>$model->id))
	)


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
<Br>

<div class="profile_genres">

	<?php

	if ($UserSubscrition['is_subscribed']==='yes') {
		echo MTLang::instance()->t("subscribe_yes",'persistence');
		foreach ($genres as $genreId => $genre) {
			echo isset($userGenres[$genreId]) ? $genre['name'].", " : "";
		}
	}
	else {
		echo MTLang::instance()->t("subscribe_no",'persistence');
	}
	?>

</div>
<Br>
<?php if (!empty($model->facebook_id)) {?>
		<a target="_blank" href="https://www.facebook.com/app_scoped_user_id/<?php echo $model->facebook_id;?>/">
			<img id="fb_squarepic" width="32" height="32" alt="Facebook" src="https://graph.facebook.com/<?php echo $model->facebook_id;?>/picture" title="Facebook">
		</a>
<br><br>
<?php }?>


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


