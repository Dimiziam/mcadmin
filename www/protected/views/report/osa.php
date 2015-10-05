<?php
// Get report translations
$translations = MTLang::instance()->getGroup('report');

?>

<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'search-form',
	)); ?>

	<div class="row">
		<h2><?php echo $translations['title_osa']; ?></h2>
	</div>

	<div class="row">

		<?php echo $translations['date_from']; ?>:
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'name' => 'dateFrom',
			'value' => $dateFrom,
			// additional javascript options for the date picker plugin
			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
				'changeMonth' => true,
				'changeYear' => true,
			),
			'htmlOptions' => array(
				'style' => 'height:20px;',
			),
		));
		?>
		&nbsp;
		<?php echo $translations['date_to']; ?>:
		<?php

		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'name' => 'dateTo',
			'value' => $dateTo,
			// additional javascript options for the date picker plugin
			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
				'changeMonth' => true,
				'changeYear' => true,
			),
			'htmlOptions' => array(
				'style' => 'height:20px;'
			),
		));
		?>
		currency: <input type="text" name="currency" value="<?php echo Yii::app()->request->getParam('currency'); ?>">
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>">
		<input type="submit" value="<?php echo MTLang::t('download_report', 'persistence'); ?>" name="getReport">
	</div>



	<?php
	// Add pager widget

	$this->widget('application.components.widgets.WPager', array('provider' => $osaReportData));

	$this->endWidget();

	if (!empty($osaReportData)) {
		$total = 0;
		$payment_to_osa=0;
		$osaReportDatas = $osaReportData->getData()
		?>


		<table class="table table-striped">
			<tr>
				<th>#</th>
				<th><?php echo $translations['created']; ?></th>
				<th><?php echo $translations['email']; ?></th>
				<th><?php echo $translations['singer_name']; ?></th>
				<th><?php echo $translations['song_title']; ?></th>
				<th><?php echo $translations['lable']; ?></th>
				<th><?php echo $translations['rule_number']; ?></th>
				<th><?php echo $translations['price']; ?></th>
				<th><?php echo $translations['downloaded']; ?></th>
				<th><?php echo $translations['cost']; ?></th>
				<th><?php echo $translations['AO_jednotka']; ?></th>
				<th><?php echo $translations['payment_to_osa']; ?></th>
			</tr>
			<?php foreach ($osaReportDatas as $item) {
				$total += round($item['cost'] * Yii::app()->request->getParam('currency'), 2);
				?>
				<tr>
					<td><?php echo $item['id']; ?></td>
					<td nowrap><?php echo date("Y-m-d", strtotime($item['created'])); ?></td>
					<td nowrap><?php echo $item['email']; ?></td>
					<td nowrap><?php echo $item['singer_name']; ?></td>
					<td nowrap><?php echo $item['song_title']; ?></td>
					<td nowrap><?php echo $item['lable']; ?></td>
					<td><?php echo $item['rule_number']; ?><br><?php echo $item['permit_from']; ?></td>
					<td><?php echo $price = round($item['price'] * Yii::app()->request->getParam('currency'), 2); ?></td>
					<td><?php echo $item['download']; ?></td>
					<td><?php echo $cost=round($item['cost'] * Yii::app()->request->getParam('currency'), 2); ?></td>
					<td><?php echo $to_pay = $price * Yii::app()->params['AO_ednotka']; ?></td>
					<td>
						<?php
						if ($to_pay < Yii::app()->params['AO_minimup']) {
							echo $new_pay = round(Yii::app()->params['AO_minimup'] * $item['download'],2);
						} else {
							echo $new_pay =round($to_pay * $item['download']);
						}

						$payment_to_osa +=$new_pay;
						?>
					</td>

				</tr>
			<?php } ?>
			<tr>
				<td colspan="7"></td>
				<td nowrap><?php echo $translations['total_on_this_page'].":".$total ?> </td>
				<td nowrap>OSA:<b><?php echo $payment_to_osa ?></b></td>


			</tr>
		</table>
	<?php } ?>

</div>



