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
			'value' => $dateFrom,
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
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>">
	</div>



	<?php
	// Add pager widget

	$this->widget('application.components.widgets.WPager', array('provider' => $osaReportData));

	$this->endWidget();

	if (!empty($osaReportData)) {
		$osaReportDatas = $osaReportData->getData()
	?>


	<table class="list_table">
		<tr>
			<th><?php echo$translations['created']; ?></th>
			<th><?php echo $translations['singer_name']; ?></th>
			<th><?php echo $translations['song_title']; ?></th>
			<th><?php echo $translations['lable']; ?></th>
			<th><?php echo $translations['rule_number']; ?></th>
			<th><?php echo $translations['downloaded']; ?></th>
			<th><?php echo $translations['cost']; ?></th>
		</tr>
		<?php  foreach ($osaReportDatas as $item) { ?>
			<tr>
				<td nowrap><?php echo  date("Y-m-d",strtotime($item['created'])); ?></td>
				<td nowrap><?php echo $item['singer_name']; ?></td>
				<td nowrap><?php echo $item['song_title']; ?></td>
				<td nowrap><?php echo $item['lable']; ?></td>
				<td><?php echo $item['rule_number']; ?></td>
				<td><?php echo $item['download']; ?></td>
				<td><?php echo $item['cost']; ?></td>

			</tr>
		<?php }  ?>
	</table>
	<?php } ?>

</div>
