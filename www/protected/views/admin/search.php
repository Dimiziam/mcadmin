<?php 
	// Get admin translations
	$translations = MTLang::instance()->getGroup('admin');
	
	// Prepare module menu
	$this->menu = array(
		array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/admin/add/')),
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
		<?php echo $translations['login']; ?>:
		<?php 
			$this->widget('CAutoComplete',
				array(
					'model' => 'SearchAdminForm',
					'name' => 'login',
					'value' => !empty($model->login) ? $model->login : '',
					'url' => $this->createUrl('/admin/autocomplete/'),
					'minChars' => 2,
					'max' => 10,
					'htmlOptions' => array(
						'size' => 50
					),
					'methodChain' => '.result(function(event,item){$("#SearchAdminForm_id").attr("value", item[1]);})'
				)
			);
			
			echo CHtml::hiddenField('SearchAdminForm[id]', !empty($model->id) ? $model->id : 0);
		?>
	</div>
	
	<div class="row buttons">
		<input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>" onClick='$("#pager_currentPageField").attr("value", 1);'>
	</div>

</div>

<?php
	if (!empty($admins)) {
		// Get admins items
		$items = $admins->getData();
		
		// Add pager widget
		$this->widget('application.components.widgets.WPager', array('provider' => $admins));
?>
	<table style="width: 300px;" class="list_table">
		<tr>
			<th>ID</th>
			<th><?php echo $translations['login']; ?></th>
			<th colspan="2">&nbsp;</th>
		</tr>
		<?php foreach ($items as $item) { ?>
			<tr id="admin_<?php echo $item['id']; ?>">
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['login']; ?></td>
				<td>
					<a href="<?php echo $this->createUrl('/admin/edit/', array('id' => $item['id'])); ?>" title="<?php echo $translations['edit_link_title']; ?>">
						<img src="/images/edit_16.png" border="0">
					</a>
				</td>
				<td>
					<a href="JavaScript: void(0);" onClick="admin_delete(<?php echo $item['id']; ?>);" title="<?php echo $translations['delete_link_title']; ?>">
						<img src="/images/trash_16.png" border="0">
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['admins_not_found']; ?></h2>
<?php } ?>
	
<?php $this->endWidget(); ?>