<?php 
	// Get user translations
	$translations = MTLang::instance()->getGroup('user');
	
	// Prepare module menu
	$this->menu = array(
		array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/user/add/')),
	);
?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'search-form',
)); ?>

<div class="form">
	
	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->emailField($model, 'email'); ?>
		<?php echo $form->error($model, 'email'); ?>
	</div>
	
	<div class="row buttons">
		<input type="submit" value="<?php echo $translations['find_button']; ?>" onClick='$("#pager_currentPageField").attr("value", 1);'>
	</div>

</div>

<?php
	if (!empty($users)) {
		// Get users items
		$items = $users->getData();
		
		$statuses = array(
			'yes' => MTLang::t('yes_value', 'persistence'),
			'no' => '<b>' . MTLang::t('no_value', 'persistence') . '</b>'
		);
		
		// Add pager widget
		$this->widget('application.components.widgets.WPager', array('provider' => $users));
?>
	<table>
		<tr>
			<th>ID</th>
			<th><?php echo $translations['email']; ?></th>
			<th><?php echo $translations['name']; ?></th>
			<th><?php echo $translations['last_name']; ?></th>
			<th><?php echo $translations['amount']; ?></th>
			<th><?php echo $translations['is_active']; ?></th>
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
				<td><?php echo $statuses[$item['is_active']]; ?></td>
				<td><?php echo $statuses[$item['is_email_confirmed']]; ?></td>
				<td><?php echo $item['created']; ?></td>
				<td>
					<a href="<?php echo $this->createUrl('/user/edit/', array('id' => $item['id'])); ?>" title="<?php echo $translations['edit_link_title']; ?>">
						<img src="/images/edit_16.png" border="0">
					</a>
				</td>
				<td>
					<a href="JavaScript: void(0);" onClick="delete_user(<?php echo $item['id']; ?>);" title="<?php echo $translations['delete_link_title']; ?>">
						<img src="/images/trash_16.png" border="0">
					</a>
				</td>
				<td>
					<a href="<?php echo $this->createUrl('/user/payments/', array('id' => $item['id'])); ?>" title="<?php echo $translations['payments_link_title']; ?>">
						<img src="/images/coins_16.png" border="0">
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['users_not_found']; ?></h2>
<?php } ?>
	
<?php $this->endWidget(); ?>