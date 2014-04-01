<?php 
	// Get user translations
	$translations = MTLang::instance()->getGroup('user');
	
	// Prepare menu items
	$items = array(
		'profile' => array(
			'url' => $this->createUrl('/user/profile/'),
			'title' => $translations['profile']
		),
		'orders' => array(
			'url' => $this->createUrl('/order/list/'),
			'title' => $translations['orders']
		),
		'transactions' => array(
			'url' => $this->createUrl('/payment/transactions/'),
			'title' => $translations['payments']
		),
		'topup' => array(
			'url' => $this->createUrl('/payment/topup/'),
			'title' => $translations['topup']
		),
	);
?>
<ul>
	<?php foreach ($items as $key => $item) { ?>
		<li> <a href="<?php echo $item['url'];?>" <?php if (!empty($currentKey) && $key == $currentKey) {echo "class='selected_user_menu'";} ?>><?php echo $item['title']; ?></a>
	<?php } ?>
</ul>