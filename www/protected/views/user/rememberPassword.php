<h2><?php echo MTLang::t('f_password', 'user'); ?></h2>

<?php $this->renderPartial('application.views.include.messages'); ?>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'signup-form',
	'enableAjaxValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
		'validateOnChange' => true
	),
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->emailField($model, 'email', array('maxlength' => 150, 'size' => 30)); ?>
		<?php echo $form->error($model, 'email'); ?>
	</div>
	
		<div class="row">
		<img src="<?php echo $this->createUrl('/captcha/display/', array('key' => 'user', 'rand' => rand(1, 1000000)));?>" id='captcha_image'><br>
		<a href="JavaScript: void(0);" onClick="tools_refresh_captcha('captcha_image', 'user');"><?php echo MTLang::t('refresh_captcha', 'user'); ?></a>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'captcha'); ?>
		<?php echo $form->textField($model, 'captcha'); ?>
		<?php echo $form->error($model, 'captcha'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton(MTLang::t('send_instruction', 'user')); ?>
	</div>
	
<?php $this->endWidget(); ?>

</div><!-- form -->