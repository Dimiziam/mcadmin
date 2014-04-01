<?php 
	// Get user translations
	$translations = MTLang::instance()->getGroup('user');

	// Start login form
	$form = $this->beginWidget('CActiveForm', 
		array(
			'id' => 'login',
			'action' => '/user/signin/',
		)
	);
	
	// Create signin user model
	$model = new SigninForm('signin');
?>
	<table>
		<tr>
			<th colspan='2'><?php echo $translations['login']; ?></th>
		</tr>
		<tr>
			<td><?php echo $translations['email']; ?></td>
			<td>
				<input type="text" name="SigninForm[email]" value="">
			</td>
		</tr>
		<tr>
			<td><?php echo $translations['password']; ?></td>
			<td>
				<input type="password" name="SigninForm[password]" value="" autocomplete="off" />
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<button name="login_submit" type="submit"><?php echo $translations['login']; ?></button><br>
				<a href="<?php echo $this->createUrl('/user/rememberPassword/');?>"><?php echo $translations['f_password']; ?></a>
			</td>
		</tr>
	</table>
<?php $this->endWidget(); ?>