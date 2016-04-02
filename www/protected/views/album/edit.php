<?php
/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 02.04.2016
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */

// Get user translations
$translations = MTLang::instance()->getGroup('album');



//$this->breadcrumbs = array(
//	$translations['singer_list'] => $this->createUrl('/singerSeo/search/'),
//	MTLang::t('add_new', 'persistence')
//);

//$this->menu = array(
//	array('label' => $translations['singer_list'], 'url' => $this->createUrl('/singerSeo/search/'))
//);
?>

<h1><?php echo $translations['edit_premit_album']; ?></h1>
<?php echo $this->renderPartial('_form', array('albumsPermits'=>$albumsPermits,'action'=>$action,'translations' => $translations)); ?>



<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'albumpermit-form',
	'action'=>'/'.Yii::app()->language.'/'.$this->action->controller->id.'/'.$action.'/',
	'enableClientValidation' => true,
	'clientOptions' => array(	
		'validateOnSubmit' => true,
	)
)); ?>



<?php
echo 'ID:'.$albumsPermits->id.' <b>'.$albumsPermits->title.'<b>';
?>
	<div class="row">
		<label><?php echo MTLang::t('album_search_permit', 'album');?></label>
		<?php echo CHtml::textField('album[permit_from]',  $albumsPermits->permit_from ); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('search',array('name'=>'album[save]','value'=>''.(MTLang::t('save', 'album').''))); ?>
	</div>

	<?php echo CHtml::hiddenField('album[id]',$albumsPermits->id ); ?>

<?php $this->endWidget(); ?>
</div>