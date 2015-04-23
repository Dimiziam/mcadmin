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
		$this->widget('zii.widgets.jui.CJuiDatePicker',array(
			'name'=>'dateFrom',
			// additional javascript options for the date picker plugin
			'options'=>array(
				'showAnim'=>'fold',
				'dateFormat'=>'yy-mm-dd'
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;'
			),
		));
		?>
		&nbsp;
		<?php echo $translations['date_to']; ?>:
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker',array(
			'name'=>'dateTo',
			// additional javascript options for the date picker plugin
			'options'=>array(
				'showAnim'=>'fold',
				'dateFormat'=>'yy-mm-dd'
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;'
			),
		));
		?>
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>">
	</div>

	<?php $this->endWidget(); ?>

</div>