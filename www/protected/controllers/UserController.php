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
				'actions' => array('search', 'add', 'edit', 'delete', 'autocomplete','affiliate','AffiliateList'),
				'users' => array('@')
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

		if(Yii::app()->request->isAjaxRequest && isset($_GET['q'])) {


			// Prepare criteria
			$criteria = new CDbCriteria;
			//if we come from affiliate

			if (!empty($_GET['f']) && ($_GET['f']=='affiliate')) {
				$criteria->condition = "email LIKE :email and is_affiliate='yes'";
			}
			else {
				$criteria->condition = "email LIKE :email";
			}

			$criteria->params = array(":email" => $_GET['q'] . "%");
			$criteria->limit = $_GET['limit'];

			// Find users
			$users = User::model()->findAll($criteria);
			if (empty($users)) {
				exit;
			}

			// Send user
			foreach ($users as $user) {
				echo $user->email . '|' . $user->id . "\n";
			}

		}

		Yii::app()->end();
	}
	
	/**
	 * Action to delete user or mark it as disabled
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
			$result->message = MTLang::t('no_id', 'user');
			$this->sendJson($result);
		}
		
		// Get user model
		$model = User::model()->findByPk($id);
		if (empty($model)) {
			$result->message = MTLang::t('not_found', 'user');
			$this->sendJson($result);
		}
		
		// Check is need to delete user
		if ((empty($model->amount) || $model->amount == '0.00') && !Order::model()->getNumberByuserId($model->id)) {
			// Delete
			if ($model->delete()) {
				$result->status = 'deleted';
			} else {
				$result->message = MTLang::t('message_3', 'user');
			}
		} else {
			$model->is_active = 'no';
			
			// Save
			if ($model->save()) {
				$result->status = 'disabled';
			} else {
				$result->message = MTLang::t('message_4', 'user');
			}
		}
		
		$this->sendJson($result);
}
	
	/**
	 * Action to add new user
	 * 
	 * @return void
	 */
	public function actionAdd()
	{
		$model = new User('create');

		if (isset($_POST['User'])) {
			$model->attributes = $_POST['User'];
			
			// Generate new secret key
			$model->secret_key = md5(uniqid(microtime() . rand(1, 10000)));
			
			// Encrypt password
			$model->password = md5(
				Yii::app()->params['secret_key'] 
				. md5($model->secret_key . $model->password . Yii::app()->params['secret_key'])
				. $model->secret_key
			);
			
			if ($model->save()) {
				$this->redirect(array('/user/search/'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_2', 'user'));
			}
		}

		$this->render('add', array('model' => $model));
	}
	
	/**
	 * Action to edit user
	 * 
	 * @param integer $id
	 * @return void
	 */
	public function actionEdit($id)
	{
		$model = $this->loadModel($id);
		$model->scenario = 'update';
		$model->f = Yii::app()->request->getParam('f');





		if (isset($_POST['User'])) {
			// Store old password
			$oldPassword = $model->password;
			
			// Set attributes
			$model->attributes = $_POST['User'];
			
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

				if ($model->f=='affiliate') {
					$this->redirect(array('/user/affiliate/'));
				}
					else {
						$this->redirect(array('/user/search/'));
					}
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_1', 'user'));

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
		$model = User::model()->findByPk($id);
		
		if ($model === null) {
			throw new CHttpException(404, MTLang::t('not_found', 'user'));
		}
		
		return $model;
	}
	
	/**
	 * Action to search users
	 * 
	 * @return void
	 */
	public function actionSearch()
	{
		$params = array();
		
		// Get user model
		$model = new SearchUserForm('search');
		
		if (!empty($_REQUEST['SearchUserForm'])) {
			$model->attributes = $_REQUEST['SearchUserForm'];
			
			// Add search by id
			if (!empty($model->id)) {
				$params['id'] = $model->id;
				
				// Get user by id
				$user = User::model()->findByPk($model->id);
				
				// Set user email
				if (!empty($user)) {
					$model->email = $user->email;
				}
			}
		}

		// Render view
		$this->render('search', 
			array(
				'model' => $model,
				'users' => User::model()->search($params)
			)
		);
	}

	/**
	 * method to view new affiliates
	 */

	public function actionAffiliate() {

		$params['is_affiliate']='yes';

		// Render view
		$this->render('affiliate',
			array(
				'users' => User::model()->search($params)
			)
		);


	}

	/**
	 * Method to view all affiliated users
	 * @param $id
	 */

	public function actionAffiliateList($id) {
		// get list affiliate users

		$params['affiliate_id']= (int)$id;

		$this->render('affiliatelist',
			array(
				'users' => User::model()->search($params),
				'affiliate_user' => User::model()->search(array('id'=>(int)$id))
			)
		);


	}


}