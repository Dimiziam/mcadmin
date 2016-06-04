<?php
/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 31.07.2015
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */
//echo "<pre>";
//$this->action->controller->id;

?>

<div class="form">

<?php

$action='search';

$form = $this->beginWidget('CActiveForm', array(
	'id' => 'albumpermit-form',
	'action'=>'/'.Yii::app()->language.'/'.$this->action->controller->id.'/'.$action.'/',
	'enableClientValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
	)
)); ?>


	<div class="row">
		<label><?php echo MTLang::t('album_search_by_name', 'album');?></label>
		<?php
		$this->widget('CAutoComplete',
			array(
				'model' => 'album',
				'name' => 'album[name]',
				'value' => !empty($model->name) ? $model->name : '',
				'url' => $this->createUrl('/albumSeo/autocomplete/'),
				'minChars' => 2,
				'max' => 10,
				'htmlOptions' => array(
					'size' => 50
				),
				'methodChain' => '.result(function(event,item){$("#albumSeo_album_id").attr("value", item[1]);})'
			)
		);
		echo CHtml::hiddenField('album[id]', !empty($albumsPermits) ? $albumsPermits->id : "", array('id' => 'albumSeo_album_id'));
		?>
	</div>
	<div class="row">
		<label><?php echo MTLang::t('album_search_by_id', 'album');?></label>
		<?php echo CHtml::textField('album[id_manual]',!empty($albumsPermits) ? $albumsPermits->id : ""); ?>
	</div>


<div class="row buttons">
	<?php echo CHtml::submitButton('search',array('name'=>'album[search]','value'=>''.(MTLang::t('search', 'album').''))); ?>
</div>



<?php $this->endWidget(); ?>
</div>