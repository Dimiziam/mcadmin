Hi.<br><br>

You was successfully registered on server <?php echo $_SERVER['SERVER_NAME']; ?><br>
Your's authorizations parameters:<br>
Email: <?php echo $model->email; ?><br>
Password: <?php echo $unencryptedPassword; ?><br>
Please remember them.<br><br>

To finish your registrations you has to confirm your's email.<br>
<?php 
	// Prepare confirm URL
	$url = $this->createUrl('/user/confirmEmail/', array('id' => $model->id, 'token' => $model->approve_token_mail)); 
?>
To do this click by <a href='<?php echo $url; ?>' target="_blank">this</a> link or copy next URL into your's browser address bar:<br>
<?php echo $url; ?>

Best regards.
