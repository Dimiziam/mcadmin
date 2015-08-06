<?php
/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 31.07.2015
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */
?>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'singerseo-form',
	'enableClientValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
	)
)); ?>


	<div class="row">
		Singer search:
		<?php
		$this->widget('CAutoComplete',
			array(
				'model' => 'SingerSeo',
				'name' => 'name',
				'value' => !empty($model->singerName) ? $model->singerName : '',
				'url' => $this->createUrl('/singerSeo/autocomplete/'),
				'minChars' => 2,
				'max' => 10,
				'htmlOptions' => array(
					'size' => 50
				),
				'methodChain' => '.result(function(event,item){$("#SingerSeo_singer_id").attr("value", item[1]);})'
			)
		);
		//echo CHtml::hiddenField('SearchUserForm[id]', !empty($model->id) ? $model->id : 0, array('id' => 'SearchUserForm_id'));
		?>
	</div>



<?php $this->renderPartial('application.views.include.messages'); ?>
<?php echo $form->errorSummary($model); ?>

<div class="row">
	<?php echo $form->labelEx($model, 'singer_id'); ?>
	<?php echo $form->textField($model, 'singer_id', array('maxlength' => 10, 'size' => 10)); ?>
	<?php echo $form->error($model, 'singer_id'); ?>
</div>


<div class="row">
	<?php echo $form->labelEx($model, 'title'); ?>
	<?php echo $form->textField($model, 'title', array('maxlength' => 255, 'size' => 70)); ?>
	<?php echo $form->error($model, 'title'); ?>
</div>

<div class="row">
	<?php echo $form->labelEx($model, 'description'); ?>
	<?php echo $form->textField($model, 'description', array('maxlength' => 255, 'size' => 70)); ?>
	<?php echo $form->error($model, 'description'); ?>
</div>


<div class="row">
		<?php echo $form->labelEx($model, 'text_seo'); ?><br>
		<?php
		$this->widget('application.extensions.tinymce.ETinyMce', array(
			'model' => $model,
			'attribute' => 'text_seo',
			'language' => 'ru',
			'editorTemplate'=>'full',
			'htmlOptions' => array('style'=>'width: 600px; height: 600px;')
		));
		?>
		<?php echo $form->error($model, 'text_seo'); ?>
</div>


<div class="row buttons">
	<?php echo CHtml::submitButton($model->isNewRecord ? MTLang::t('add_button', 'persistence') : MTLang::t('save_button', 'persistence')); ?>
</div>

<?php $this->endWidget(); ?>

</div>