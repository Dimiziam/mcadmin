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

		<?php if (!empty($f) and $f == 'affiliate') {

			$this->breadcrumbs = array(
				MTLang::t('affiliate_list', 'user') . ": " => $this->createUrl('/user/affiliatelist/', array('id' => $model->id)),
				$model->email
			);


		} else {
			?>
			<div class="row form-inline">
				<?php echo $translations['transaction_id']; ?>:
				<?php echo $form->textField($model, 'id',array("class"=>"form-control")); ?>
			</div>

		<?php } ?>
		<div class="row form-inline">
			<?php echo $translations['user_email']; ?>:
			<?php
			$this->widget('CAutoComplete',
				array(
					'model' => 'SearchTransactionForm',
					'name' => 'email',
					'value' => !empty($model->email) ? $model->email : '',
					'url' => $this->createUrl('/user/autocomplete/'),
					'minChars' => 2,
					'max' => 10,
					'htmlOptions' => array(
						'size' => 50,
						'class' =>'form-control'
					),
					'methodChain' => '.result(function(event,item){$("#SearchTransactionForm_user_id").attr("value", item[1]);})'
				)
			);
			echo "&nbsp;";
			echo CHtml::dropDownList('SearchTransactionForm[billing]',
					$model->billing, $model->getEnumList('billing'),
				array("class"=>"form-control")
			);

			echo CHtml::hiddenField('SearchTransactionForm[user_id]', !empty($model->user_id) ? $model->user_id : 0);
			?>
		</div>

		<div class="row buttons">
			<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>"
				   onClick='$("#pager_currentPageField").attr("value", 1);'>
		</div>

	</div>

<?php
if (!empty($transactions)) {
	// Get transactions items
	$items = $transactions->getData();


	// Add pager widget
	$this->widget('application.components.widgets.WPager', array('provider' => $transactions));
	?>
	<table class="table table-striped">
		<tr>
			<th>ID</th>
			<th><?php echo $translations['email']; ?></th>
			<th><?php echo $translations['billing']; ?></th>
			<th><?php echo $translations['amount']; ?></th>
			<th><?php echo $translations['status']; ?></th>
			<th><?php echo $translations['date_start']; ?></th>
			<th><?php echo $translations['date_end']; ?></th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr>
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['email']; ?></td>
				<td><?php echo $translations['billing_' . $item['billing']]; ?></td>
				<td><?php echo $item['type'] == 'add' ? '+' . $item['amount'] : '-' . $item['amount']; ?></td>
				<td><?php echo $orderTranslations['status_' . $item['status']]; ?></td>
				<td><?php echo $item['date_start']; ?></td>
				<td><?php echo $item['date_end']; ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['transactions_not_found']; ?></h2>
<?php } ?>

<?php $this->endWidget(); ?>