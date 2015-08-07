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
	array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/singerSeo/add/')),
);
?>

	<script>
		var global_messages = {
			'admin': {
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
			<?php echo $translations['singer']; ?>:
			<?php
			$this->widget('CAutoComplete',
				array(
					'model' => 'SearchSingerSeoForm',
					'name' => 'singer_name',
					'value' => !empty($model->singer_name) ? $model->singer_name : '',
					'url' => $this->createUrl('/singerSeo/autocomplete/'),
					'minChars' => 2,
					'max' => 10,
					'htmlOptions' => array(
						'size' => 50
					),
					'methodChain' => '.result(function(event,item){$("#SearchSingerSeoForm_singer_id").attr("value", item[1]);})'
				)
			);

			//echo CHtml::hiddenField('SearchSingerSeoForm[singer_id]', !empty($model->singer_id) ? $model->singer_id : 0);
			echo CHtml::hiddenField('SearchSingerSeoForm[singer_id]');
			?>
		</div>

		<div class="row buttons">
			<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>"
				   onClick='$("#pager_currentPageField").attr("value", 1);'>
		</div>

	</div>

<?php
if (!empty($singersSeo)) {
	// Get admins items
	$items = $singersSeo->getData();

	// Add pager widget
	$this->widget('application.components.widgets.WPager', array('provider' => $singersSeo));
	?>
	<table style="width: 100%;" class="table table-striped">
		<tr>
			<th width="30px">ID</th>
			<th width="20px"><?php echo $translations['singer_id']; ?></th>
			<th width="150px"><?php echo $translations['singer_name']; ?></th>
			<th><?php echo $translations['info']; ?></th>
			<th  width="20px">&nbsp;</th>
			<th  width="20px">&nbsp;</th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr id="admin_<?php echo $item['id']; ?>">
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['singer_id']; ?></td>
				<td><?php echo $item['name']; ?></td>
				<td>
					<b>title:</b><?php echo $item['title']; ?><br>
					<b>keywords:</b><?php echo $item['keywords']; ?><br>
					<b>description:</b><?php echo $item['description']; ?><br>
					<b>seo_text:</b><?php echo $item['text_seo']; ?><br>

				</td>


				<td>
					<a href="<?php echo $this->createUrl('/singerSeo/edit/', array('id' => $item['id'])); ?>"
					   title="<?php echo $translations['edit_link_title']; ?>">
						<img src="/images/edit_16.png" border="0">
					</a>
				</td>
				<td>
					<a href="JavaScript: void(0);" onClick="admin_delete(<?php echo $item['id']; ?>);"
					   title="<?php echo $translations['delete_link_title']; ?>">
						<img src="/images/trash_16.png" border="0">
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['singers_not_found']; ?></h2>
<?php } ?>

<?php $this->endWidget(); ?>