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
		<?php echo $form->labelEx($model, 'title'); ?>
		<?php echo $form->textField($model, 'title', array('maxlength' => 255, 'size' => 70)); ?>
		<?php echo $form->error($model, 'title'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'alias'); ?>
		<?php echo $form->textField($model, 'alias', array('maxlength' => 255, 'size' => 70)); ?>
		<?php echo $form->error($model, 'alias'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'url'); ?>
		<?php echo $form->textField($model, 'url', array('maxlength' => 255, 'size' => 70)); ?>
		<br><small><?php echo $translations['support_sites']; ?></small>
		<?php echo $form->error($model, 'url'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? MTLang::t('add_button', 'persistence') : MTLang::t('save_button', 'persistence')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>