<h2><?php echo MTLang::t('end_signup', 'user'); ?></h2>

<?php $this->renderPartial('application.views.include.messages'); ?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'signup-form',
	'enableAjaxValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
		'validateOnChange' => true
	),
	'action' => $this->createUrl('/user/endSignup/', array('id' => $model->id))
)); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton(MTLang::t('confirm_email', 'user'), array('name' => 'resendEmail')); ?>
	</div>

<?php $this->endWidget(); ?>