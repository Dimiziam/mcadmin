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
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->emailField($model, 'email', array('maxlength' => 150, 'size' => 70)); ?>
		<?php echo $form->error($model, 'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'password'); ?>
		<?php 
			$model->password = '';
			echo $form->textField($model, 'password', array('maxlength' => 150, 'size' => 70)); 
		?>
		<?php echo $form->error($model, 'password'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('maxlength' => 150, 'size' => 70)); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'last_name'); ?>
		<?php echo $form->textField($model, 'last_name', array('maxlength' => 150, 'size' => 70)); ?>
		<?php echo $form->error($model, 'last_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'is_active'); ?>
		<?php echo $form->dropDownList($model, 'is_active', array('yes' => MTLang::t('yes_value', 'persistence'), 'no' => MTLang::t('no_value', 'persistence'))); ?>
		<?php echo $form->error($model, 'is_active'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'is_email_confirmed'); ?>
		<?php echo $form->dropDownList($model, 'is_email_confirmed', array('yes' => MTLang::t('yes_value', 'persistence'), 'no' => MTLang::t('no_value', 'persistence'))); ?>
		<?php echo $form->error($model, 'is_email_confirmed'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'is_affiliate'); ?>
		<?php echo $form->dropDownList($model, 'is_affiliate', array('yes' => MTLang::t('yes_value', 'persistence'), 'no' => MTLang::t('no_value', 'persistence'))); ?>
		<?php echo $form->error($model, 'is_affiliate'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? MTLang::t('add_button', 'persistence') : MTLang::t('save_button', 'persistence')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>