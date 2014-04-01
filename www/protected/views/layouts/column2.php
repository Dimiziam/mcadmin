<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<table width="100%">
	<tr>
		<td width="80%"><?php echo $content; ?></td>
		<td width="20%">
			<div id="sidebar">
				<?php
					$this->beginWidget('zii.widgets.CPortlet', array(
						'title'=>'Operations',
					));
					$this->widget('zii.widgets.CMenu', array(
						'items'=>$this->menu,
						'htmlOptions'=>array('class'=>'operations'),
					));
					$this->endWidget();
				?>
			</div><!-- sidebar -->
		</td>
	</tr>
</table>
<?php $this->endContent(); ?>