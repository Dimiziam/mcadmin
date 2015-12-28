<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'search-form',
	)); ?>

	<div class="row">
		<h2><?php echo $title; ?></h2>
	</div>

	<div class="row">

		<?php echo $translations['date_from']; ?>:
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'name' => 'dateFrom',
			'value' => Yii::app()->request->getParam('dateFrom'),
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
			'value' => Yii::app()->request->getParam('dateTo'),
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



	<div class="row_label">
		Show only eu
		<?php echo CHtml::checkBox('show_only_eu',Yii::app()->request->getParam('show_only_eu')); ?>
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>">
		<!-- <input type="submit" value="<?php echo MTLang::t('download_report', 'persistence'); ?>" name="getReport"> -->
	</div>
<?php
	// Add pager widget
$this->widget('application.components.widgets.WPager', array('provider' => $ReportData));
$this->endWidget();

?>