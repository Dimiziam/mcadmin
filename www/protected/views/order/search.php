<?php 
	// Get order translations
	$translations = MTLang::instance()->getGroup('order');
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
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>" onClick='$("#pager_currentPageField").attr("value", 1);'>
	</div>

</div>

<?php
	if (!empty($orders)) {
		// Get orders items
		$items = $orders->getData();
		
		$statuses = array(
			'preparing' => $translations['status_preparing'],
			'processing' => $translations['status_processing'],
			'done' => $translations['status_done'],
			'error' => $translations['status_error'],
			'canceld' => $translations['status_canceld']
		);
		
		// Add pager widget
		$this->widget('application.components.widgets.WPager', array('provider' => $orders));
?>
	<table class="list_table">
		<tr>
			<th>ID</th>
			<th><?php echo $translations['email']; ?></th>
			<th><?php echo $translations['songs_number']; ?></th>
			<th><?php echo $translations['total_price']; ?></th>
			<th><?php echo $translations['status']; ?></th>
			<th><?php echo $translations['created']; ?></th>
			<th>&nbsp;</th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr>
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['email']; ?></td>
				<td><?php echo $item['songs_number']; ?></td>
				<td><?php echo $item['price']; ?></td>
				<td><?php echo $statuses[$item['status']]; ?></td>
				<td><?php echo $item['created']; ?></td>
				<td>
					<a href="<?php echo $this->createUrl('/order/songs/', array('id' => $item['id'])); ?>" title="<?php echo $translations['songs_link_title']; ?>">
						<img src="/images/songs_16.png" border="0">
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['orders_not_found']; ?></h2>
<?php } ?>
	
<?php $this->endWidget(); ?>