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
		<?php echo $form->labelEx($model, 'position'); ?>
		<?php echo $form->textField($model, 'position', array('size' => 20)); ?>
		<?php echo $form->error($model, 'position'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'singer'); ?>
		<?php 
			$this->widget('CAutoComplete',
				array(
					'model' => 'ChartItem',
					'name' => 'ChartItem[singer]',
					'value' => !empty($model->singer) ? $model->singer : '',
					'url' => $this->createUrl('/singer/autocomplete/'),
					'minChars' => 2,
					'max' => 10,
					'htmlOptions' => array(
						'size' => 50
					),
					'methodChain' => '.result(function(event,item){$("#ChartItem_singer_id").attr("value", item[1]);})'
				)
			);
			
			echo CHtml::hiddenField('ChartItem[singer_id]', !empty($model->singer_id) ? $model->singer_id : 0);
		?>
		<?php echo $form->error($model, 'singer'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'album'); ?>
		<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',
				array(
					'model' => 'ChartItem',
					'name' => 'ChartItem[album]',
					'value' => !empty($model->album) ? $model->album : '',
					'source' => 'js: function(request, response) {
						$.ajax({
							url: "' . $this->createUrl('/album/autocomplete/') . '",
							dataType: "json",
							data: {
								q: request.term,
								singer_id: $("#ChartItem_singer_id").val()
							},
							success: function (data) {
								response(data);
							}
						})
					}',
					'options' => array(
						'select' => 'js:function(event, ui){$("#ChartItem_album_id").attr("value", ui.item.id);}'
					),
					'htmlOptions' => array(
						'size' => 50
					)
				)
			);
			
			echo CHtml::hiddenField('ChartItem[album_id]', !empty($model->album_id) ? $model->album_id : 0);
		?>
		<?php echo $form->error($model, 'album'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? MTLang::t('add_button', 'persistence') : MTLang::t('save_button', 'persistence')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>