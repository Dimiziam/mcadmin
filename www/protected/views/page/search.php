<?php 
	// Get static_page translations
	$translations = MTLang::instance()->getGroup('static_page');
	
	// Prepare module menu
	$this->menu = array(
		array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/page/add/', array('type' => $type))),
	);
?>

<script>
	var global_messages = {
		'pages': {
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
		<?php echo $translations['language']; ?>:
		<?php 
			$model->language = !empty($model->language) ? $model->language : Yii::app()->language;
			echo $form->dropDownList($model, 'language', Yii::app()->params['languages']);
		?>
	</div>
	
	<div class="row buttons">
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>" onClick='$("#pager_currentPageField").attr("value", 1);'>
	</div>

</div>

<?php
	if (!empty($pages)) {
		// Get users items
		$items = $pages->getData();
		
		// Add pager widget
		$this->widget('application.components.widgets.WPager', array('provider' => $pages));
?>
	<table class="list_table">
		<tr>
			<th>ID</th>
			<th><?php echo $translations['alias']; ?></th>
			<th><?php echo $translations['title']; ?></th>
			<th><?php echo $translations['created']; ?></th>
			<th colspan="2">&nbsp;</th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr id="page_<?php echo $item['id']; ?>">
				<td><?php echo $item['id']; ?></td>
				<td><?php echo CHtml::encode($item['alias']); ?></td>
				<td><?php echo !empty($item['current_title']) ? CHtml::encode($item['current_title']) : CHtml::encode($item['default_title']); ?></td>
				<td><?php echo $item['created']; ?></td>
				<td width="20px">
					<a href="<?php echo $this->createUrl('/page/edit/', array('id' => $item['id'], 'lang' => $language, 'type' => $type)); ?>" title="<?php echo $translations['edit_link_title']; ?>">
						<img src="/images/edit_16.png" border="0">
					</a>
				</td>
				<td width="20px">
					<a href="JavaScript: void(0);" onClick="page_delete(<?php echo $item['id']; ?>);" title="<?php echo $translations['delete_link_title']; ?>">
						<img src="/images/trash_16.png" border="0">
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['pages_not_found']; ?></h2>
<?php } ?>
	
<?php $this->endWidget(); ?>
