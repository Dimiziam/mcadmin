<?php 
	// Get top menu translations
	$translations = MTLang::instance()->getGroup('user');
?>
<ul>
	<li class="no-margin-left">
		<div class="reg_div">
			<a href="#" id="reg-button"><?php echo $translations['reg']; ?></a>
		</div>

		<div id="AjaxLoader" class="loader_register" style="display: none">
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/spinner.gif" />
		</div>

		<div class="reg-form" id="reg-form">
			<?php
				// Start registration form
				$form = $this->beginWidget('CActiveForm', 
					array(
						'id' => 'reg',
						'enableAjaxValidation' => true,
						'clientOptions' => array(
							'validateOnSubmit' => true,
							'validateOnChange' => true
						),
						'action' => '/user/signup/',
					)
				);
				
				// Create signup user model
				$model = new SignupForm('signup');
			?>
				<fieldset id=form_reg_body>
					<?php echo $form->errorSummary($model); ?>
					<fieldset>
						<?php echo $form->error($model, 'email'); ?>
						<input type="text" name="SignupForm[email]" id="SignupForm_email" value="<?php echo $translations['email']; ?>" class="input_mail">
					</fieldset>
					
					<fieldset>
						<?php echo $form->error($model, 'password'); ?>
						<input id="password-clear-reg" type="text" value="<?php echo $translations['password']; ?>" autocomplete="off" class="input_password" /> 
						<input id="SignupForm_password" type="password" name="SignupForm[password]" value="" autocomplete="off" class="input_password" />
					</fieldset>
					
					<fieldset>
						<?php echo $form->error($model, 'confirmPassword'); ?>
						<input id="password-clear-confirm" type="text" value="<?php echo $translations['c_password']; ?>" autocomplete="off" class="input_password" /> 
						<input id="SignupForm_confirmPassword" type="password" name="SignupForm[confirmPassword]" value="" autocomplete="off" class="input_password" />
					</fieldset>
					
					<fieldset>
						<img src="<?php echo $this->createUrl('/captcha/display/', array('key' => 'user', 'rand' => rand(1, 1000000)));?>" id='top_reg_captcha'><br>
						<a href="JavaScript: void(0);" onClick="tools_refresh_captcha('top_reg_captcha', 'user');"><?php echo MTLang::t('refresh_captcha', 'user'); ?></a>
					</fieldset>
					
					<fieldset>
						<?php echo $form->error($model, 'captcha'); ?>
						<input type="text" name="SignupForm[captcha]" id="SignupForm_captcha" value="<?php echo $translations['captcha']; ?>" class="input_mail">
					</fieldset>
		
					<fieldset>
						<button name="login_submit" class="buttons right" type="submit"><?php echo $translations['reg']; ?></button>
					</fieldset>
				</fieldset>
			<?php $this->endWidget(); ?>
		</div>
	</li>
	
	<li class="no-margin-left">
		<div id="loginContainer">
			<div class="login_div">
				<a href="#" id="login-button"><?php echo $translations['login']; ?></a>
			</div>
			<div class="login-form">
				<?php
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
					<fieldset id=form_login_body>
						<fieldset>
							<input type="text" name="SigninForm[email]" id="email" value="<?php echo $translations['email']; ?>" class="input_mail">
						</fieldset>

						<fieldset>
							<input id="password-clear" type="text" value="<?php echo $translations['password']; ?>" autocomplete="off" class="input_password" /> 
							<input id="password-password" type="password" name="SigninForm[password]" value="" autocomplete="off" class="input_password" />
						</fieldset>

						<fieldset>
							<a href="<?php echo $this->createUrl('/user/rememberPassword/');?>"><?php echo $translations['f_password']; ?></a> &nbsp;&nbsp;
							<button name="login_submit" class="buttons right" type="submit"><?php echo $translations['login']; ?></button>
						</fieldset>
					</fieldset>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</li>
</ul>