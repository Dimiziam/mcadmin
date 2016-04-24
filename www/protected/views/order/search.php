<?php 
	// Get order translations
	$translations = MTLang::instance()->getGroup('order');
?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'search-form',
)); ?>

<div class="form">
	
	<div class="row">
		<?php echo $translations['order_id']; ?>:
		<?php echo $form->textField($model, 'id'); ?>
	</div>
	
	<div class="row">
		<?php echo $translations['user_email']; ?>:
		<?php 
			$this->widget('CAutoComplete',
				array(
					'model' => 'SearchOrderForm',
					'name' => 'email',
					'value' => !empty($model->email) ? $model->email : '',
					'url' => $this->createUrl('/user/autocomplete/'),
					'minChars' => 2,
					'max' => 10,
					'htmlOptions' => array(
						'size' => 50
					),
					'methodChain' => '.result(function(event,item){$("#SearchOrderForm_user_id").attr("value", item[1]);})'
				)
			);
			
			echo CHtml::hiddenField('SearchOrderForm[user_id]', !empty($model->user_id) ? $model->user_id : 0);
		?>
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
	<table class="table table-striped">
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

					<a href="<?php echo $this->createUrl('/user/edit/', array('id' => $item['user_id'])); ?>">
						<img src="/images/edit_16.png" border="0">
					</a>

				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['orders_not_found']; ?></h2>
<?php } ?>
	
<?php $this->endWidget(); ?>