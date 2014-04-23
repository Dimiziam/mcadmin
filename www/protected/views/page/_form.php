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

	<div class="row">
		<?php echo $translations['language'] . ': ' . Yii::app()->params['languages'][$language]; ?>
	</div>

	<div class="row">
		<?php echo $translations['type'] . ': ' . $translations['type_' . $type]; ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($languageModel, 'title'); ?>
		<?php echo $form->textField($languageModel, 'title', array('maxlength' => 255, 'size' => 70)); ?>
		<?php echo $form->error($languageModel, 'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($languageModel, 'annotation'); ?><br>
		<?php echo $form->textArea($languageModel, 'annotation', array('cols' => 70, 'rows' => 10)); ?>
		<?php echo $form->error($languageModel, 'annotation'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($languageModel, 'page_text'); ?><br>
		<?php echo $form->textArea($languageModel, 'page_text', array('cols' => 70, 'rows' => 30)); ?>
		<?php echo $form->error($languageModel, 'page_text'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? MTLang::t('add_button', 'persistence') : MTLang::t('save_button', 'persistence')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>