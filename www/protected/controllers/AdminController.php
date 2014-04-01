<?php
/**
 * AdminController
 * 
 * Controller to work with admins
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class AdminController extends Controller {
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('logout'),
				'users' => array('@')
			),
			array('allow',
				'actions' => array('signin'),
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
		// Get admin model for signin scenario
		$model = new Admin('signin');
		
		if (!empty($_POST['Admin'])) {
			// Sret attributes
			$model->attributes = $_POST['Admin'];
			
			// Validate form data
			if ($model->validate()) {
				// Get user identity
				$identity = new MCUserIdentity($_POST['Admin']['login'], $_POST['Admin']['password']);
				
				if ($identity->authenticate()) {
					Yii::app()->user->login($identity);
					
					// Redirect user
					$this->redirect(!empty(Yii::app()->user->returnUrl) ? Yii::app()->user->returnUrl : '/');
				} else {
					Yii::app()->user->setFlash('error', MTLang::t('not_found', 'admin'));
				}
			}
		}
		
		// Render view
		$this->render('signin', array('model' => $model));
	}
}