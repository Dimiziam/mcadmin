<?php 
	// Get transaction translations
	$translations = MTLang::instance()->getGroup('transaction');
	
	// Get order translations
	$orderTranslations = MTLang::instance()->getGroup('order');
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
	if (!empty($transactions)) {
		// Get transactions items
		$items = $transactions->getData();
		
		$statuses = array(
			'preparing' => $orderTranslations['status_preparing'],
			'processing' => $orderTranslations['status_processing'],
			'done' => $orderTranslations['status_done'],
			'error' => $orderTranslations['status_error'],
			'canceld' => $orderTranslations['status_canceld']
		);
		
		// Add pager widget
		$this->widget('application.components.widgets.WPager', array('provider' => $transactions));
?>
	<table class="list_table">
		<tr>
			<th>ID</th>
			<th><?php echo $translations['email']; ?></th>
			<th><?php echo $translations['amount']; ?></th>
			<th><?php echo $translations['status']; ?></th>
			<th><?php echo $translations['date_start']; ?></th>
			<th><?php echo $translations['date_end']; ?></th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr>
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['email']; ?></td>
				<td><?php echo $item['type'] == 'add' ? '+' . $item['amount'] : '-' . $item['amount']; ?></td>
				<td><?php echo $statuses[$item['status']]; ?></td>
				<td><?php echo $item['date_start']; ?></td>
				<td><?php echo $item['date_end']; ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['transactions_not_found']; ?></h2>
<?php } ?>
	
<?php $this->endWidget(); ?>