<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<table width="100%">
	<tr>
		<td width="80%" style="vertical-align: top"><?php echo $content; ?></td>
		<td width="20%" style="vertical-align: top">
			<?php
				$this->beginWidget('zii.widgets.CPortlet', array(
					'title' => MTLang::t('operations', 'persistence')
				));
				$this->widget('zii.widgets.CMenu', array(
					'items'=>$this->menu,
					'htmlOptions'=>array('class'=>'operations'),
				));
				$this->endWidget();
			?>
		</td>
	</tr>
</table>
<?php $this->endContent(); ?>