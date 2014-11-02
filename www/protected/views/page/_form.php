<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'player-form',
	'enableClientValidation' => true,
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

	<?php if ($type == 'news') { ?>
		<div class="row">
			<?php echo $form->labelEx($languageModel, 'annotation'); ?><br>
			<?php echo $form->textArea($languageModel, 'annotation', array('cols' => 70, 'rows' => 10)); ?>
			<?php echo $form->error($languageModel, 'annotation'); ?>
		</div>
	<?php } ?>
	
	<div class="row">
		<?php echo $form->labelEx($languageModel, 'page_text'); ?><br>
		<?php 
			$this->widget('application.extensions.tinymce.ETinyMce', array(
				'model' => $languageModel,
				'attribute' => 'page_text',
				'language' => 'ru',
				'htmlOptions' => array('style'=>'width: 600px; height: 200px;')
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
$('#languages_select').change(function() {
	window.location = '/' + global_language_code + '/page/edit/' + $(this).val() + '_<?php echo $type; ?>_<?php echo $model->id; ?>.html';
});
</script>