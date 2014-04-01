<h2><?php echo MTLang::t('authorization', 'admin'); ?></h2>

<?php $this->renderPartial('application.views.include.messages'); ?>

<div class="form">
	
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'signup-form',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true
		),
	)); ?>
	
		<?php echo $form->errorSummary($model); ?>
		
		<div class="row">
			<?php echo $form->labelEx($model, 'login'); ?>
			<?php echo $form->textField($model, 'login'); ?>
			<?php echo $form->error($model, 'login'); ?>
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model, 'password'); ?>
			<?php 
				$model->password = '';
				echo $form->passwordField($model, 'password', array('maxlength' => 32, 'size' => 30));
			?>
			<?php echo $form->error($model, 'password'); ?>
		</div>
		
		<div class="row buttons">
			<?php echo CHtml::submitButton(MTLang::t('enter', 'admin')); ?>
		</div>
		
	<?php $this->endWidget(); ?>

</div><!-- form -->