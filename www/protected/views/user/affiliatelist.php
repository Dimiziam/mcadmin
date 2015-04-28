<?php
// Get user translations
$translations = MTLang::instance()->getGroup('user');

$affiliate_user = $affiliate_user->rawData;


$this->breadcrumbs = array(
	$translations['affiliate_h2'] => $this->createUrl('/user/affiliate/') ,
	$affiliate_user[0]['email']
);

?>
<h2><?php echo $translations['affiliate_list']; ?></h2>
<?php
$this->widget('application.components.widgets.WPager', array('provider' => $users));



if (!empty($users)) {
// Get users items
$items = $users->getData();

	$statuses = array(
		'yes' => MTLang::t('yes_value', 'persistence'),
		'no' => '<b>' . MTLang::t('no_value', 'persistence') . '</b>'
	);


?>



	<div class="">
		<b><?php echo $translations['affiliate_login']; ?></b>: <?php echo $affiliate_user[0]['email'];?>(<?php echo $affiliate_user[0]['name']." ".$affiliate_user[0]['last_name'];?>)
		<b><?php echo $translations['affiliatedUDS']; ?> <?php echo $affiliate_user[0]['affiliate_amount'];?></b>


			&nbsp; <a href="<?php echo $this->createUrl('/transaction/affiliate/', array( 'id'=>$affiliate_user[0]['id'])); ?>" title="<?php echo $translations['affiliate_orders']; ?>">
					<img src="/images/list_16.png" border="0">
				</a>

		</td>

	</div>

	<br>



<table class="table table-striped">
	<tr>
		<th>ID</th>
		<th><?php echo $translations['email']; ?></th>
		<th><?php echo $translations['name']; ?></th>
		<th><?php echo $translations['last_name']; ?></th>
		<th><?php echo $translations['amount']; ?></th>
		<th><?php echo $translations['is_active']; ?></th>
		<th><?php echo $translations['is_affiliate']; ?></th>
		<th><?php echo $translations['is_email_confirmed']; ?></th>
		<th><?php echo $translations['created']; ?></th>


		<th colspan="3">&nbsp;</th>
	</tr>
	<?php foreach ($items as $item) { ?>
		<tr id="user_<?php echo $item['id']; ?>">
			<td><?php echo $item['id']; ?></td>
			<td><?php echo $item['email']; ?></td>
			<td><?php echo $item['name']; ?></td>
			<td><?php echo $item['last_name']; ?></td>
			<td><?php echo $item['amount']; ?></td>
			<td id="user_status_<?php echo $item['id']; ?>"><?php echo $statuses[$item['is_active']]; ?></td>
			<td><?php echo $statuses[$item['is_affiliate']]; ?></td>
			<td><?php echo $statuses[$item['is_email_confirmed']]; ?></td>
			<td><?php echo $item['created']; ?></td>
		</tr>
	<?php } ?>
</table>
<?php } else { ?>
	<h2><?php echo $translations['users_not_found']; ?></h2>
<?php } ?>








