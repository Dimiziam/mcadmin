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
		<?php echo $form->labelEx($model, 'login'); ?>
		<?php echo $form->textField($model, 'login', array('maxlength' => 150, 'size' => 70)); ?>
		<?php echo $form->error($model, 'login'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'password'); ?>
		<?php 
			$model->password = '';
			echo $form->textField($model, 'password', array('maxlength' => 150, 'size' => 70)); 
		?>
		<?php echo $form->error($model, 'password'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? MTLang::t('add_button', 'persistence') : MTLang::t('save_button', 'persistence')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>