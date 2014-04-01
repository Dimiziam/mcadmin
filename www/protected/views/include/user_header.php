<?php 
	// Get user info
	$userInfo = Yii::app ()->user->getInfo();
	
	// Get top menu translations
	$translations = MTLang::instance()->getGroup('user');
?>
<ul>
	<li class="no-margin-left">
			<a href="<?php echo $this->createUrl('/user/profile/');?>"><?php echo $translations['profile']; ?></a>
	</li>
	<li >
		<a href="#" id="reg-button"><?php echo $userInfo['email']; ?></a><span class="header_amount">&nbsp;($<?php echo $userInfo['amount'];?>)</span>
	</li>

	<li >
			<a href="<?php echo $this->createUrl('/user/logout/');?>" id="reg-button"><?php echo $translations['logout']; ?></a>
	</li>
</ul>
