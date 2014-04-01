<h2><?php echo MTLang::t('change_password', 'user'); ?></h2>

<?php $this->renderPartial('application.views.include.messages'); ?>

<?php if ($init == 'success') { ?>
	<b><?php echo MTLang::t('enter_new_password', 'user'); ?></b>
	
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
		
		<input type='hidden' name='id' value='<?php echo $model->id; ?>' />
		<input type='hidden' name='token' value='<?php echo $model->approve_token_mail; ?>' />
		
		<div class="row">
			<?php echo $form->labelEx($model, 'password'); ?>
			<?php 
				$model->password = '';
				echo $form->passwordField($model, 'password', array('maxlength' => 32, 'size' => 30));
			?>
			<?php echo $form->error($model, 'password'); ?>
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model, 'confirmPassword'); ?>
			<?php 
				$model->confirmPassword = '';
				echo $form->passwordField($model, 'confirmPassword', array('maxlength' => 32, 'size' => 30));
			?>
			<?php echo $form->error($model, 'confirmPassword'); ?>
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
			<?php echo CHtml::submitButton(MTLang::t('change_password', 'user')); ?>
		</div>
		
	<?php $this->endWidget(); ?>
	
	</div><!-- form -->
<?php } ?>