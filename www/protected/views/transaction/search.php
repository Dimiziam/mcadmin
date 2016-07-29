<?php
// Get transaction translations
$translations = MTLang::instance()->getGroup('transaction');

//
$translationsUser = MTLang::instance()->getGroup('user');
// Get order translations
$orderTranslations = MTLang::instance()->getGroup('order');


?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'search-form',
	'action'=>Yii::app()->createUrl('/transaction/search/')
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
				<?php echo $form->textField($model, 'id', array("class" => "form-control")); ?>
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
						'class' => 'form-control'
					),
					'methodChain' => '.result(function(event,item){$("#SearchTransactionForm_user_id").attr("value", item[1]);})'
				)
			);
			echo "&nbsp;";
			echo CHtml::dropDownList('SearchTransactionForm[billing]',
				$model->billing, $model->getEnumList('billing'),
				array("class" => "form-control", 'empty' => '-')
			);
			echo "&nbsp;";
			echo CHtml::dropDownList('SearchTransactionForm[status]',
				$model->status, $model->getEnumList('status'),
				array("class" => "form-control", 'empty' => '-')
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
			<th><?php echo $translations['info']; ?></th>
			<th><?php echo $translations['date_start']; ?><br><?php echo $translations['date_end']; ?></th>
			<th></th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr>
				<td><?php echo $item['id']; ?></td>
				<td>
					<?php echo $item['email']; ?><br>
					<?php echo $item['country_code']; ?>,
					<span class="bold 	<?php if ($item['download_all'] == 'yes') {
						echo " green";
					} ?>
					"><?php echo $item['download_all']; ?></span><br>
				</td>
				<td><?php echo $translations['billing_' . $item['billing']]; ?></td>
				<td><?php echo $item['type'] == 'add' ? '+' . $item['amount'] : '-' . $item['amount']; ?></td>
				<td>
					<?php echo $orderTranslations['status_' . $item['status']]; ?>
					<?php
					if ($item['status'] == 'done' and in_array($item['billing'], array('paypal', 'payu'))) {
						echo "<a class='confirmation' href=".$this->createUrl('/transaction/canceled/',array("transaction_id"=>$item['id'])).">".$translations['refund']."</a>";
						}
					?>
				</td>
				<td nowrap class="paymentinfo">
					<?php


					if (!empty($item['info'])) {
						$payment_detail_info = $item['info'] ?  unserialize($item['info']) : "";


						if (!empty($payment_detail_info)) {
							if ($item['billing'] === 'payu') {
								echo($payment_detail_info['ts']);
							} elseif ($item['billing'] === 'paypal') {
								echo 	'PaymentID: '.$payment_detail_info['paymentId'].'<br>';
								if (!empty($payment_detail_info['payerId'])) {
								echo	'PayerID: ' . $payment_detail_info['payerId'] . '<br>' .
									'<b>payment detail </b> ' . '<br>' .
									'Email: ' . $payment_detail_info['detailPayment']['email'] . '<br>' .
									'Last Name: ' . $payment_detail_info['detailPayment']['last_name'] . '<br>' .
									'First Name: ' . $payment_detail_info['detailPayment']['first_name'] . '<br>' .
									'County Code: ' . $payment_detail_info['detailPayment']['county_code'] . '<br>';
								}
							}
						}

					}


					?>
				</td>
				<td nowrap>
					<?php echo $item['date_start']; ?><br>
					<?php echo $item['date_end']; ?>
				</td>
				<td nowrap>

					<a href="<?php echo $this->createUrl('/user/edit/', array('id' => $item['user_id'])); ?>"
					   title="<?php echo $translationsUser['edit_link_title']; ?>">
						<img src="/images/edit_16.png" border="0">
					</a>
					&nbsp;
					<a href="<?php echo $this->createUrl('/transaction/search/',
						array('SearchTransactionForm[user_id]' => $item['user_id'],
							'SearchTransactionForm[status]' => '',
							'SearchTransactionForm[billing]' => '')); ?>"
					   title="<?php echo $translationsUser['payments_link_title']; ?>">
						<img src="/images/coins_16.png" border="0">
					</a>
					&nbsp;

					<a href="<?php echo $this->createUrl('/order/search/', array('SearchOrderForm[user_id]' => $item['user_id'])); ?>">
						<img src="/images/cart_16.png" border="0">
					</a>

				</td>


			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['transactions_not_found']; ?></h2>
<?php } ?>

<?php $this->endWidget(); ?>