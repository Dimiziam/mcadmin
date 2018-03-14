<?php 
	// Get chart translations
	$translations = MTLang::instance()->getGroup('chart');
	
	// Prepare breadcrumbs
	$this->breadcrumbs = array(
		$translations['charts_list'] => $this->createUrl('/chart/search/'),
		$chart->title => $this->createUrl('/chart/edit/', array('id' => $chart->id)),
		$translations['chart_items_list']
	);
	
	// Prepare module menu
	$this->menu = array(
		array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/chart/itemAdd/', array('created' => $created, 'sourceId' => $chart->id))),
	);
?>

<script>
	var global_messages = {
		'chart_item': {
			'delete_confirm': "<?php echo $translations['item_delete_confirm']; ?>",
			'delete_error': "<?php echo $translations['message_5']; ?>"
		}
	};
</script>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'search-form',
)); ?>

<div class="form">
	
	<div class="row">
		<?php echo $translations['year']; ?>:
		<?php 
			// Prepare years
			$years = array();
			for($i=0; $i<=10; $i++) {
				$year = date('Y', time()) - $i;
				$years[$year] = $year;
			}
		
			// Set current year
			$model->year = date('Y', strtotime($created));
			
			echo $form->dropDownList($model, 'year', $years); 
		?>
		<?php echo $translations['month']; ?>:
		<?php 
			// Prepare months
			$months = array();
			for($i=1; $i<=12; $i++) {
				$month = $i > 9 ? $i : '0' . $i;
				$months[$month] = $month;
			}
		
			// Set current month
			$model->month = date('m', strtotime($created));
			
			echo $form->dropDownList($model, 'month', $months); 
		?>
	</div>
	
	<div class="row buttons">
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>" onClick='$("#pager_currentPageField").attr("value", 1);'>
	</div>

</div>

<div>
	<input type="hidden" name="need_refresh" id="need_refresh" />
	<input type="submit" value="<?php echo $translations['load_from_site']; ?>" onClick='$("#need_refresh").attr("value", 1);'>
</div>

<?php $this->renderPartial('application.views.include.messages'); ?>

<?php
	if (!empty($items)) {
		// Get charts items
		$chartItems = $items->getData();
		
		// Add pager widget
		$this->widget('application.components.widgets.WPager', array('provider' => $items));
?>
	<table style="width: 300px;" class="table table-striped">
		<tr>
			<th>ID</th>
			<th><?php echo $translations['position']; ?></th>
			<th><?php echo $translations['singer']; ?></th>
			<th><?php echo $translations['album']; ?></th>
			<th colspan="2">&nbsp;</th>
		</tr>
		<?php foreach ($chartItems as $item) { ?>
			<tr id="chart_item_<?php echo $item['id']; ?>">
				<td><?php echo CHtml::encode($item['id']); ?></td>
				<td><?php echo CHtml::encode($item['position']); ?></td>
				<td><nobr><?php echo CHtml::encode($item['singer']) . (!empty($item['singer_id']) ? ' (<a href="https://www.mediaclub.com/en/singer/' . $item['singer_id'] . '" target=_blank>' . $item['singer_id'] . ')</a>' : ''); ?></nobr></td>
				<td><nobr><?php echo CHtml::encode($item['album']) . (!empty($item['album_id']) ? ' (<a href="https://www.mediaclub.com/en/album/'. $item['album_id'] .'" target=_blank>' . $item['album_id'] . '</a>)' : ''); ?></nobr></td>
				<td>
					<a href="<?php echo $this->createUrl('/chart/itemEdit/', array('id' => $item['id'])); ?>" title="<?php echo $translations['item_edit_link_title']; ?>">
						<img src="/images/edit_16.png" border="0">
					</a>
				</td>
				<td>
					<a href="JavaScript: void(0);" onClick="chart_item_delete(<?php echo $item['id']; ?>);" title="<?php echo $translations['item_delete_link_title']; ?>">
						<img src="/images/trash_16.png" border="0">
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['chart_items_not_found']; ?></h2>
<?php } ?>
	
<?php $this->endWidget(); ?>