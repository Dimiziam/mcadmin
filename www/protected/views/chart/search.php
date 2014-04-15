<?php 
	// Get chart translations
	$translations = MTLang::instance()->getGroup('chart');
	
	// Prepare module menu
	$this->menu = array(
		array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/chart/add/')),
	);
?>

<script>
	var global_messages = {
		'chart': {
			'delete_confirm': "<?php echo $translations['delete_confirm']; ?>",
			'delete_error': "<?php echo $translations['message_1']; ?>"
		}
	};
</script>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'search-form',
)); ?>

<div class="form">
	
	<div class="row">
		<?php echo $translations['title']; ?>:
		<?php 
			$this->widget('CAutoComplete',
				array(
					'model' => 'SearchChartSourceForm',
					'name' => 'title',
					'value' => !empty($model->title) ? $model->title : '',
					'url' => $this->createUrl('/chart/autocomplete/'),
					'minChars' => 2,
					'max' => 10,
					'htmlOptions' => array(
						'size' => 50
					),
					'methodChain' => '.result(function(event,item){$("#SearchChartSourceForm_id").attr("value", item[1]);})'
				)
			);
			
			echo CHtml::hiddenField('SearchChartSourceForm[id]', !empty($model->id) ? $model->id : 0);
		?>
	</div>
	
	<div class="row buttons">
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>" onClick='$("#pager_currentPageField").attr("value", 1);'>
	</div>

</div>

<?php
	if (!empty($charts)) {
		// Get charts items
		$items = $charts->getData();
		
		// Add pager widget
		$this->widget('application.components.widgets.WPager', array('provider' => $charts));
?>
	<table style="width: 300px;" class="list_table">
		<tr>
			<th>ID</th>
			<th><?php echo $translations['alias']; ?></th>
			<th><?php echo $translations['title']; ?></th>
			<th><?php echo $translations['url']; ?></th>
			<th colspan="3">&nbsp;</th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr id="chart_<?php echo $item['id']; ?>">
				<td><?php echo CHtml::encode($item['id']); ?></td>
				<td><?php echo CHtml::encode($item['alias']); ?></td>
				<td><?php echo CHtml::encode($item['title']); ?></td>
				<td>
					<a href="<?php echo urldecode($item['url']); ?>" title="<?php echo $translations['goto_link_title']; ?>" target="_blank">
						<nobr><?php echo CHtml::encode($item['url']); ?></nobr>
					</a>
				</td>
				<td>
					<a href="<?php echo $this->createUrl('/chart/edit/', array('id' => $item['id'])); ?>" title="<?php echo $translations['edit_link_title']; ?>">
						<img src="/images/edit_16.png" border="0">
					</a>
				</td>
				<td>
					<a href="JavaScript: void(0);" onClick="chart_delete(<?php echo $item['id']; ?>);" title="<?php echo $translations['delete_link_title']; ?>">
						<img src="/images/trash_16.png" border="0">
					</a>
				</td>
				<td>
					<a href="<?php echo $this->createUrl('/chart/items/', array('id' => $item['id'])); ?>" title="<?php echo $translations['items_link_title']; ?>">
						<img src="/images/list_16.png" border="0">
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['charts_not_found']; ?></h2>
<?php } ?>
	
<?php $this->endWidget(); ?>