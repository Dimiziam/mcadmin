Hi.<br><br>

<p>You are resived this mail because of someon on server <b><?php echo $_SERVER['SERVER_NAME']; ?></b>
try to restore password for current email address.</p>

<p>
<?php 
	// Prepare confirm URL
	$url = $this->createUrl('/user/changePassword/', array('id' => $model->id, 'token' => $model->approve_token_mail)); 
?>
To restore your password click on <a href='<?php echo $url; ?>' target="_blank">this</a> link or copy next URL into your's browser address bar:<br>
<?php echo $url; ?>
</p>

Best regards.
