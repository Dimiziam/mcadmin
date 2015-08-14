<?php
/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 06.08.2015
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */

// Get admin translations
$translations = MTLang::instance()->getGroup('seo');

// Prepare module menu
$this->menu = array(
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/albumSeo/add/')),
);
?>

	<script>
		var global_messages = {
			'albumSeo': {
				'delete_confirm': "<?php echo $translations['delete_confirm']; ?>",
				'delete_error': "<?php echo $translations['message_3']; ?>"
			}
		};
	</script>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'search-form',
)); ?>

	<div class="form">

		<div class="row">
			<?php echo $translations['album']; ?>:
			<?php
			$this->widget('CAutoComplete',
				array(
					'model' => 'SearchAlbumSeoForm',
					'name' => 'album_name',
					'value' => !empty($model->name) ? $model->name : '',
					'url' => $this->createUrl('/albumSeo/autocomplete/'),
					'minChars' => 2,
					'max' => 10,
					'htmlOptions' => array(
						'size' => 50
					),
					'methodChain' => '.result(function(event,item){$("#SearchAlbumSeoForm_album_id").attr("value", item[1]);})'
				)
			);

			//echo CHtml::hiddenField('SearchSingerSeoForm[singer_id]', !empty($model->singer_id) ? $model->singer_id : 0);
			echo CHtml::hiddenField('SearchAlbumSeoForm[album_id]');
			?>
		</div>

		<div class="row buttons">
			<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>"
				   onClick='$("#pager_currentPageField").attr("value", 1);'>
		</div>

	</div>

<?php
if (!empty($albumsSeo)) {
	// Get admins items
	$items = $albumsSeo->getData();

	// Add pager widget
	$this->widget('application.components.widgets.WPager', array('provider' => $albumsSeo));
	?>
	<table style="width: 100%;" class="table table-striped">
		<tr>
			<th width="30px">ID</th>
			<th width="30px"><?php echo $translations['album_id']; ?></th>
			<th width="200px"><?php echo $translations['album_name']; ?></th>
			<th><?php echo $translations['info']; ?></th>
			<th width="20px">&nbsp;</th>
			<th width="20px">&nbsp;</th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr id="albumSeo_<?php echo $item['id']; ?>">
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['album_id']; ?></td>
				<td>
					Album:
					<a target="_blank"
					   href="<?php echo Yii::app()->params['url_main_site']; ?>en/album/<?php echo $item['album_id']; ?>">
						<?php echo $item['name']; ?>
					</a>
					<br>
					Singer:
					<a target="_blank"
					   href="<?php echo  Yii::app()->params['url_main_site'];?>en/singer/<?php echo $item['singer_id'];?>">
						<?php echo $item['sname']; ?><br>
					</a>
				</td>
				<td>
					<b>title:</b><?php echo $item['title']; ?><br>
					<b>keywords:</b><?php echo $item['keywords']; ?><br>
					<b>description:</b><?php echo $item['description']; ?><br>
					<b>seo_text:</b><?php echo $item['text_seo']; ?><br>

				</td>


				<td>
					<a href="<?php echo $this->createUrl('/albumSeo/edit/', array('id' => $item['id'])); ?>"
					   title="<?php echo $translations['edit_link_title']; ?>">
						<img src="/images/edit_16.png" border="0">
					</a>
				</td>
				<td>
					<a href="JavaScript: void(0);" onClick="albumSeo_delete(<?php echo $item['id']; ?>);"
					   title="<?php echo $translations['delete_link_title']; ?>">
						<img src="/images/trash_16.png" border="0">
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['albums_not_found']; ?></h2>
<?php } ?>

<?php $this->endWidget(); ?>