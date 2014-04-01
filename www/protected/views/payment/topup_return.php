<?php
	// Get order translations
	$translations = MTLang::instance()->getGroup('transaction');
	
	// Get user info
	$userInfo = Yii::app ()->user->getInfo();
?>

<h2><?php echo $translations['topup_return_page_caption']; ?></h2>

<table width="100%">
<tr>
	<td width="20%"><?php $this->renderPartial('application.views.user._profile.menu', array('currentKey' => 'topup')); ?></td>
	<td>
		<h3><?php echo sprintf($translations['current_amount'], ($userInfo['amount'] . '$')); ?></h3>
		
		<?php $this->renderPartial('application.views.include.messages'); ?>
		
	</td>
</tr>
</table>