<?php
// Get user translations
$translations = MTLang::instance()->getGroup('transaction');

$this->breadcrumbs = array(
	MTLang::t('users_list', 'user') => $this->createUrl('/user/search/'),
	$translations['transactions_of_user'] . ': ' . $user->email => $this->createUrl('/transaction/search/', array('SearchTransactionForm[user_id]' => $user->id)),
	$translations['change_balance'] . ': ' . $user->email
);

$this->menu = array(
	array('label' => MTLang::t('users_list', 'user'), 'url' => $this->createUrl('/user/search/')),
	array('label' => $translations['transactions_of_user'] . ': ' . $user->email, 'url' => $this->createUrl('/transaction/search/', array('SearchTransactionForm[user_id]' => $user->id)))
);
?>

<h1><?php echo $translations['change_balance'] . ': ' . $user->email; ?></h1>

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
		<?php echo $form->labelEx($model, 'amount'); ?>
		<?php echo $form->textField($model, 'amount', array('maxlength' => 150, 'size' => 50)); ?>
		<?php echo $form->error($model, 'amount'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'type'); ?>
		<?php echo $form->dropDownList($model, 'type', array('add' => $translations['type_add'], 'sub' => $translations['type_sub'])); ?>
		<?php echo $form->error($model, 'type'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($translations['change_balance_button']); ?>
	</div>

<?php $this->endWidget(); ?>

</div>