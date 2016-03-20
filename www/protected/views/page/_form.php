<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'player-form',
		'enableClientValidation' => true,

		'htmlOptions' => array('enctype' => 'multipart/form-data'),
		'clientOptions' => array(
			'validateOnSubmit' => true,
		)
	)); ?>

	<?php $this->renderPartial('application.views.include.messages'); ?>
	<?php echo $form->errorSummary($model); ?>

	<?php if (!$model->isNewRecord) { ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'language'); ?>
			<?php echo $form->dropDownList($model, 'language', Yii::app()->params['languages'], array('id' => 'languages_select')); ?>
			<?php echo $form->error($model, 'language'); ?>
		</div>
	<?php } else { ?>
		<div class="row">
			<?php echo $translations['language'] . ': ' . Yii::app()->params['languages'][$language]; ?>
		</div>
	<?php } ?>

	<?php if (!$model->isNewRecord) { ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'alias'); ?>
			<?php echo $form->textField($model, 'alias', array('maxlength' => 255, 'size' => 70)); ?>
			<?php echo $form->error($model, 'alias'); ?>
		</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($languageModel, 'title'); ?>
		<?php echo $form->textField($languageModel, 'title', array('maxlength' => 255, 'size' => 70)); ?>
		<?php echo $form->error($languageModel, 'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($languageModel, 'meta_description'); ?>
		<?php echo $form->textField($languageModel, 'meta_description', array('maxlength' => 555, 'size' => 70)); ?>
		<?php echo $form->error($languageModel, 'meta_description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($languageModel, 'keyword_image'); ?>
		<?php echo $form->textField($languageModel, 'keyword_image', array('maxlength' => 555, 'size' => 70)); ?>
		<?php echo $form->error($languageModel, 'keyword_image'); ?>
	</div>

	<?php if (!$model->isNewRecord) {
		// Image shown here if page is update page
		?>
		<?php if (!empty($languageModel->image)) { ?>
			<div class="row">
				<?php echo Yii::app()->request->getBaseUrl(); ?>
				<?php echo CHtml::image(Yii::app()->params['news_image_path'] . $languageModel->image, 'image', array('width' => 200)); ?>
				<div class="row">
					<?php echo $form->labelEx($languageModel, 'remove_image'); ?>
					<?php echo $form->checkbox($languageModel, 'remove_image', false, array('value' => 'on')); ?>
				</div>

			</div>
		<?php } ?>
	<?php } ?>


	<?php if ($type == 'news') { ?>
		<div class="row">
			<?php echo $form->labelEx($languageModel, 'image'); ?>
			<?php echo $form->fileField($languageModel, 'image'); ?>
			<?php echo $form->error($languageModel, 'image'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($languageModel, 'annotation'); ?><br>
			<?php echo $form->textArea($languageModel, 'annotation', array('cols' => 70, 'rows' => 10)); ?>
			<?php echo $form->error($languageModel, 'annotation'); ?>
		</div>


<div class="row">
	<?php echo $form->labelEx($model, 'publishDate'); ?><br>
	<?php
	$this->widget('zii.widgets.jui.CJuiDatePicker', array(
		'name' => Chtml::activeName($model, 'publishDate'),
		'value' => $model->publishDate,
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

	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($languageModel, 'page_text'); ?><br>
		<?php
		$this->widget('application.extensions.tinymce.ETinyMce', array(
			'model' => $languageModel,
			'attribute' => 'page_text',
			'language' => 'ru',
			'editorTemplate' => 'full',
			'htmlOptions' => array('style' => 'width: 600px; height: 600px;')
		));
		?>
		<?php echo $form->error($languageModel, 'page_text'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? MTLang::t('add_button', 'persistence') : MTLang::t('save_button', 'persistence')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

<script>
	$('#languages_select').change(function () {
		window.location = '/' + global_language_code + '/page/edit/' + $(this).val() + '_<?php echo $type; ?>_<?php echo $model->id; ?>.html';
	});
</script>