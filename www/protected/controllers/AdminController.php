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
				'actions' => array('logout', 'search', 'add', 'edit', 'delete', 'autocomplete'),
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
	 * Displays a particular model.
	 * 
	 * @param integer $id the ID of the model to be displayed
	 * @return void
	 */
	public function actionAutocomplete()
	{
		// Prepare criteria
		$criteria = new CDbCriteria;
		$criteria->condition = "login LIKE :login";
		$criteria->params = array(":login" => $_GET['q'] . "%");
		$criteria->limit = $_GET['limit'];
		
		// Find admins
		$admins = Admin::model()->findAll($criteria);
		if (empty($admins)) {
			exit;
		}
		
		// Send admin
		foreach ($admins as $admin) {
			echo $admin->login . '|' . $admin->id . "\n";
		}
		
		Yii::app()->end();
	}
	
	/**
	 * Action to delete admin
	 * 
	 * @param int $id
	 * @return void
	 */
	public function actionDelete($id)
	{
		// Prepare result
		$result = new stdClass;
		$result->status = 'error';
		
		// Check id
		if (empty($id)) {
			$result->message = MTLang::t('no_id', 'admin');
			$this->sendJson($result);
		}
		
		// Get admin model
		$model = Admin::model()->findByPk($id);
		if (empty($model)) {
			$result->message = MTLang::t('not_found', 'admin');
			$this->sendJson($result);
		}
		
		// Delete
		if ($model->delete()) {
			$result->status = 'deleted';
		} else {
			$result->message = MTLang::t('message_3', 'admin');
		}
		
		$this->sendJson($result);
	}
	
	/**
	 * Action to add new admin
	 * 
	 * @return void
	 */
	public function actionAdd()
	{
		$model = new Admin('create');

		if (isset($_POST['Admin'])) {
			$model->attributes = $_POST['Admin'];
			
			// Generate new secret key
			$model->secret_key = md5(uniqid(microtime() . rand(1, 10000)));
			
			// Encrypt password
			$model->password = md5(
				Yii::app()->params['secret_key'] 
				. md5($model->secret_key . $model->password . Yii::app()->params['secret_key'])
				. $model->secret_key
			);
			
			if ($model->save()) {
				$this->redirect(array('/admin/search/'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_2', 'admin'));
			}
		}

		$this->render('add', array('model' => $model));
	}
	
	/**
	 * Action to edit admin
	 * 
	 * @param integer $id
	 * @return void
	 */
	public function actionEdit($id)
	{
		$model = $this->loadModel($id);
		$model->scenario = 'update';
		
		if (isset($_POST['Admin'])) {
			// Store old password
			$oldPassword = $model->password;
			
			// Set attributes
			$model->attributes = $_POST['Admin'];
			
			// Prepare new password
			if (!empty($model->password)) {
				// Generate new secret key
				$model->secret_key = md5(uniqid(microtime() . rand(1, 10000)));
				
				// Encrypt password
				$model->password = md5(
					Yii::app()->params['secret_key'] 
					. md5($model->secret_key . $model->password . Yii::app()->params['secret_key'])
					. $model->secret_key
				);
			} else {
				$model->password = $oldPassword;
			}
			
			if ($model->save()) {
				$this->redirect(array('/admin/search/'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_1', 'admin'));
			}
		}

		$this->render('edit', array('model' => $model));
	}
	
	/**
	 * Method return loaded model by id
	 * 
	 * @param integer the ID of the model to be loaded
	 * @return CActiveRecord
	 */
	public function loadModel($id)
	{
		$model = Admin::model()->findByPk($id);
		
		if ($model === null) {
			throw new CHttpException(404, MTLang::t('not_found', 'admin'));
		}
		
		return $model;
	}
	
	/**
	 * Action to search admins
	 * 
	 * @return void
	 */
	public function actionSearch()
	{
		$params = array();
		
		// Get user model
		$model = new SearchAdminForm('search');
		
		if (!empty($_REQUEST['SearchAdminForm'])) {
			$model->attributes = $_REQUEST['SearchAdminForm'];
			
			// Add search by id
			if (!empty($model->id)) {
				$params['id'] = $model->id;
				
				// Get admin by id
				$admin = Admin::model()->findByPk($model->id);
				
				// Set admin login
				if (!empty($admin)) {
					$model->login = $admin->login;
				}
			}
		}
		
		// Render view
		$this->render('search', 
			array(
				'model' => $model,
				'admins' => Admin::model()->search($params)
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