<?php
/**
 * UserController
 * 
 * Controller to work with users
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class UserController extends Controller {
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('logout', 'profile'),
				'users' => array('@')
			),
			array('allow',
				'actions' => array(
					'signup', 'signin', 'endSignup', 'confirmEmail', 'rememberPassword',
					'changePassword'
				),
				'users' => array('?')
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * Action to logout player
	 * 
	 * @return void
	 */
	public function actionLogout()
	{
		// Logout user from session
		Yii::app()->user->logout();
		
		// Prepare params
		$expireTime = time() - Yii::app()->params['cookies']['life_time'];
		$path = '/';
		$domain = '.' . str_replace('www.', '', $_SERVER['SERVER_NAME']);
		$prefix = Yii::app()->params['cookies']['prefix'];
		
		// Unset cookie
		setcookie($prefix . 'userId', 0, $expireTime, $path, $domain);
		setcookie($prefix . 'authHash', '', $expireTime, $path, $domain);
		
		// Redirect to home page
		$this->redirect(Yii::app()->homeUrl);
	}
	
	/**
	 * Action to signin user
	 * 
	 * @return void
	 */
	public function actionSignin()
	{
		// Get player model for signin scenario
		$model = new SigninForm('signin');
		
		if (!empty($_POST['SigninForm'])) {
			// Sret attributes
			$model->attributes = $_POST['SigninForm'];
			
			// Validate form data
			if ($model->validate()) {
				// Get user identity
				$identity = new MCUserIdentity($_POST['SigninForm']['email'], $_POST['SigninForm']['password']);
				
				if ($identity->authenticate()) {
					Yii::app()->user->login($identity);
					
					// Redirect user
					$this->redirect(!empty(Yii::app()->user->returnUrl) ? Yii::app()->user->returnUrl : '/');
				} else {
					Yii::app()->user->setFlash('error', "Игрок не найден");
				}
			}
		}
		
		// Render view
		$this->render('signin', array('model' => $model));
	}
	
	/**
	 * Action to change password
	 * 
	 * @param int $id
	 * @param string $token
	 * @return void
	 */
	public function actionChangePassword($id, $token)
	{
		// Try to find user by id
		$model = User::model()->findByPk($id);
		$model->scenario = 'changePassword';
		
		if (empty($model->id)) {
			Yii::app()->user->setFlash('error', MTLang::t('not_found', 'user'));
			$this->render('changePassword', array('init' => 'error'));
			return false;
		}
		
		// Check token
		if ($model->approve_token_mail != $token) {
			Yii::app()->user->setFlash('error', MTLang::t('incorrect_token', 'user'));
			$this->render('changePassword', array('init' => 'error'));
			return false;
		}

		if (isset($_POST['User'])) {
			$model->attributes = $_POST['User'];
			
			// Ajax validation
			if(Yii::app()->getRequest()->getIsAjaxRequest()) {
				echo CActiveForm::validate(array($model)); 
				Yii::app()->end(); 
			}
			
			// Generate user secret key
			$model->secret_key = md5(uniqid(microtime() . rand(1, 1000000)));
			
			// Store unencrypted password
			$unencryptedPassword = $model->password;
			
			// Create password hash
			$model->confirmPassword = $model->password = md5(
				Yii::app()->params['secret_key'] 
				. md5($model->secret_key . $model->password . Yii::app()->params['secret_key'])
				. $model->secret_key
			);
			
			// Clear hash
			$model->approve_token_mail = '';
			
			if ($model->save()) {
				// Send confirm email
				$result = MCMailer::get($this)
					->setEmails(Yii::app()->params['mailer'], $model->email)
					->render('html', 'changePasswordMail', array('model' => $model, 'unencryptedPassword' => $unencryptedPassword))
					->send(sprintf(MTLang::t('change_password_title', 'user'), $_SERVER['SERVER_NAME']));
				
				Yii::app()->user->setFlash('success', MTLang::t('message_1', 'user'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_2', 'user'));
			}
		}

		$this->render('changePassword', array('model' => $model, 'init' => 'success'));
	}
	
	/**
	 * Action to remember password
	 * 
	 * @return void
	 */
	public function actionRememberPassword()
	{
		// Get user model
		$model = new User('rememberPassword');

		if (isset($_POST['User'])) {
			$model->attributes = $_POST['User'];
			
			// Ajax validation
			if(Yii::app()->getRequest()->getIsAjaxRequest()) {
				echo CActiveForm::validate(array($model)); 
				Yii::app()->end(); 
			}
			
			// Try to find model by email
			$model = User::model()->find('email = :email', array(':email' => $model->email));
			if (empty($model->id)) {
				Yii::app()->user->setFlash('error', MTLang::t('not_found', 'user'));
				$this->render('rememberPassword', array('model' => $model));
				return false;
			}
			
			// Create token
			$model->approve_token_mail = md5(uniqid(microtime() . rand(1, 10000000) . $model->email));
			
			// Save model
			if ($model->save()) {
				// Send remember password email
				$result = MCMailer::get($this)
					->setEmails(Yii::app()->params['mailer'], $model->email)
					->render('html', 'rememberMail', array('model' => $model))
					->send(sprintf(MTLang::t('change_password_title', 'user'), $_SERVER['SERVER_NAME']));
				
				if ($result) {
					Yii::app()->user->setFlash('success', MTLang::t('message_3', 'user'));
				} else {
					Yii::app()->user->setFlash('error', MTLang::t('message_4', 'user'));
				}
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_change_password_mail_not_sent', 'user'));
			}
		}

		$this->render('rememberPassword', array('model' => $model));
	}
	
	/**
	 * Action to confirm email
	 * 
	 * @param int $id
	 * @param sring $token
	 * @return void
	 */
	public function actionConfirmEmail($id, $token)
	{
		// Try to find user by id
		$model = User::model()->findByPk($id);
		if (empty($model->id)) {
			Yii::app()->user->setFlash('error', MTLang::t('not_found', 'user'));
			$this->render('confirmEmail');
			return false;
		}
		
		// Check email status
		if ($model->is_email_confirmed == 'yes') {
			Yii::app()->user->setFlash('error', MTLang::t('email_already_confirmed', 'user'));
			$this->render('confirmEmail');
			return false;
		}
		
		// Check token
		if ($model->approve_token_mail != $token) {
			Yii::app()->user->setFlash('error', MTLang::t('incorrect_token', 'user'));
			$this->render('confirmEmail');
			return false;
		}

		// Change user info
		$model->approve_token_mail = '';
		$model->is_email_confirmed = 'yes';
		
		// Save user info
		if (!$model->save()) {
			Yii::app()->user->setFlash('error', MTLang::t('message_5', 'user'));
			$this->render('confirmEmail');
			return false;
		}
		
		Yii::app()->user->setFlash('success', MTLang::t('message_6', 'user'));
		
		$this->render('confirmEmail');
	}
	
	/**
	 * Action to signup a new user
	 * 
	 * @return void
	 */
	public function actionSignup()
	{
		// Get user model
		$model = new SignupForm('signup');

		if (isset($_POST['SignupForm'])) {
			// Set attributes
			$model->attributes = $_POST['SignupForm'];
			
			// Ajax validation
			if(Yii::app()->getRequest()->getIsAjaxRequest()) {
				echo CActiveForm::validate(array($model)); 
				Yii::app()->end(); 
			}
			
			// Generate user secret key
			$model->secret_key = md5(uniqid(microtime() . rand(1, 1000000)));
			
			// Store unencrypted password
			$unencryptedPassword = $model->password;
			
			// Create password hash
			$model->confirmPassword = $model->password = md5(
				Yii::app()->params['secret_key'] 
				. md5($model->secret_key . $model->password . Yii::app()->params['secret_key'])
				. $model->secret_key
			);
			
			// Create user
			if ($model->save()) {
				// Send confirm email
				$result = MCMailer::get($this)
					->setEmails(Yii::app()->params['mailer'], $model->email)
					->render('html', 'confirmEmailMail', array('model' => $model, 'unencryptedPassword' => $unencryptedPassword))
					->send(sprintf(MTLang::t('c_letter_title', 'user'), $_SERVER['SERVER_NAME']));
				
				$this->redirect(array('/user/endSignup/', 'id' => $model->id, 'mailResult' => $result ? 'ok' : 'error'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('signup_error_message', 'user'));
			}
		}

		$this->render('signup', array('model' => $model));
	}
	
	/**
	 * Method to display end signup page
	 * 
	 * @param int $id
	 * @param string $mailResult
	 * @return void
	 */
	public function actionEndSignup($id, $mailResult = null)
	{
		$this->layout = '//layouts/column1';
		
		// Display messages
		if (!empty($mailResult)) {
			if ($mailResult == 'ok') {
				Yii::app()->user->setFlash('success', "Благодарим вас за регистрацию.<br>На указанный Вами email было выслано письмо для подтверждения регистрации.<br>Если письмо не пришло то воспользуйтесь кнопкой \"Подтвердить регистрацию\".");
			} else {
				Yii::app()->user->setFlash('success', "Благодарим вас за регистрацию.");
				Yii::app()->user->setFlash('error', "Нам не удалось выслать письмо для подтверждения регистрации на указанный Вами email.<br>Для повторной отправки письма нажмите кнопку \"Подтвердить регистрацию\".");
			}
		}
		
		// Get user model
		$model = User::model()->findByPk($id);
		
		if (!empty($_POST['resendEmail'])) {
			// Resend confirm email
			$result = MCMailer::get($this)
				->setEmails(Yii::app()->params['mailer'], $model->email)
				->render('html', 'resendConfirmEmailMail', array('model' => $model))
				->send(sprintf(MTLang::t('c_letter_title', 'user'), $_SERVER['SERVER_NAME']));
			
			if ($result) {
				Yii::app()->user->setFlash('success', "На указанный Вами email было повторно выслано письмо для подтверждения регистрации.<br>Если письмо не пришло то воспользуйтесь кнопкой \"Подтвердить регистрацию\".");
			} else {
				Yii::app()->user->setFlash('error', "Нам повторно не удалось выслать письмо для подтверждения регистрации на указанный Вами email.<br>Для повторной отправки письма нажмите кнопку \"Подтвердить регистрацию\".");
			}
		}
		
		$this->render('endSignup', array('model' => $model));
	}
	
	/**
	 * Action to display user profile
	 * 
	 * @return void
	 */
	public function actionProfile()
	{
		// Try to find user by id
		$model = User::model()->findByPk(Yii::app()->user->id);
		$model->scenario = 'profile';

		if (isset($_POST['User'])) {
			// Store current password
			$currentPassword = $model->password;
			
			// Set attributes
			$model->attributes = $_POST['User'];
			
			// Ajax validation
			if(Yii::app()->getRequest()->getIsAjaxRequest()) {
				echo CActiveForm::validate(array($model)); 
				Yii::app()->end(); 
			}
			
			if (!empty($model->password)) {
				// Generate user secret key
				$model->secret_key = md5(uniqid(microtime() . rand(1, 1000000)));
				
				// Create password hash
				$model->confirmPassword = $model->password = md5(
					Yii::app()->params['secret_key'] 
					. md5($model->secret_key . $model->password . Yii::app()->params['secret_key'])
					. $model->secret_key
				);
			} else {
				$model->confirmPassword = $model->password = $currentPassword;
			}
			
			// Save user
			if ($model->save()) {
				Yii::app()->user->setFlash('error', MTLang::t('message_8', 'user'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_7', 'user'));
			}
		}

		$this->render('profile', array('model' => $model));
	}
}