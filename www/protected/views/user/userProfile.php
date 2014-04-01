<?php if(Yii::app()->user->hasFlash('contact')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('contact'); ?>
</div>

<?php else: ?>




<div class="row">
		Login: <?php echo $this->user_login; ?>
</div>


<div class="row">
		<?php  echo Yii::t('intf','amount');

		echo " : ".number_format($this->amount,2);

		?>
</div>

<?php if ((Yii::app()->user->hasFlash('success'))) {
	echo Yii::app()->user->getFlash('success');
		echo "<br>";

			echo Yii::t('intf','RealName')." - ".CHtml::encode($userinfo->RealName)."<Br>";
			echo Yii::t('intf','LastName')." - ".CHtml::encode($userinfo->LastName)."<Br>";




} else {?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'Profile',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
	'validateOnSubmit'=>true,),
	));

?>

<?php echo $form->hiddenField($user_Profile_model,'myaction',array('value'=>"saveprofile")); ?>


<p class="note">Fields with <span class="required">*</span>are required.</p>

	<div class="row">
		<?php echo $form->labelEx($user_Profile_model,Yii::t('intf','RealName')); ?>
		<?php echo $form->textField($user_Profile_model,'RealName',array('value'=> CHtml::encode($userinfo->RealName))); ?>
		<?php echo $form->error($user_Profile_model,'RealName'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($user_Profile_model,Yii::t('intf','LastName')); ?>
		<?php echo $form->textField($user_Profile_model,'LastName',array('value'=> CHtml::encode($userinfo->LastName))); ?>
		<?php echo $form->error($user_Profile_model,'LastName'); ?>
	</div>




	<div class="row">
		<?php echo $form->labelEx($user_Profile_model,Yii::t('intf','password')); ?>
		<?php echo $form->passwordField($user_Profile_model,'password'); ?>
		<?php echo $form->error($user_Profile_model,'password'); ?>
	</div>

		<div class="row">
		<?php echo $form->labelEx($user_Profile_model,Yii::t('intf','password_confirm')); ?>
		<?php echo $form->passwordField($user_Profile_model,'password_confirm'); ?>
		<?php echo $form->error($user_Profile_model,'password_confirm'); ?>

	</div>


		<div class="row">
		<button name="login_submit" class="buttons right" type="submit"><?php echo Yii::t('intf','Save')?></button>

	</div>
<?php $this->endWidget(); ?>

<?php }?>






</div>


<?php endif;?>