<?php
/**
 * TransactionController
 * 
 * Controller to work with transactions
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class TransactionController extends Controller {
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('search', 'add','affiliate'),
				'users' => array('@'),
				'roles'=>array('admin'),
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}
	
	/**
	 * Action to add/sub money direct
	 * 
	 * @return void
	 */
	public function actionAdd($userId)
	{
		// Check user id
		if (empty($userId)) {
			$this->redirect(array('/transaction/search/'));
		}
		
		// Find user by id
		$user = User::model()->findByPk($userId);
		if (empty($user)) {
			$this->redirect(array('/transaction/search/'));
		}
		
		// Get model
		$model = new Transaction('add_money');

		// Process action
		if (isset($_POST['Transaction'])) {
			// Fill model
			$model->attributes = $_POST['Transaction'];
			$model->user_id = $userId;
			$model->status = 'done';
			$model->billing = 'direct';
			$model->date_start = $model->date_end = date('Y-m-d H:i:s', time());
			
			if ($model->save()) {
				Yii::app()->user->setFlash('success', MTLang::t('message_1', 'transaction'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_2', 'transaction'));
			}
		}

		$this->render('add', 
			array(
				'user' => $user,
				'model' => $model
			)
		);
	}

	/**
	 * method to get all affiliate transaction by user id
	 * @param $id int userid
	 */

	public function actionAffiliate($id){

		$email = Yii::app()->request->getParam('email');
		$user_id = Yii::app()->request->getParam('SearchTransactionForm');
		$user_id = (int)$user_id['user_id'];


		// data from form
		if (!empty($email) && !empty($user_id)) {
			$user = User::model()->findByPk($user_id);
		}
		else {
		// Get user by id by url
			$user = User::model()->findByPk($id);
		}
		// Set user email
		if (!empty($user)) {
			$params['user_id'] = $user->id;
			$params['billing'] = 'affiliate';
		}


// Render view
		$this->render('search',
			array(
				'model' => $user,
				'f'=>'affiliate',
				'transactions' => Transaction::model()->search($params)
			)
		);

	}


	/**
	 * Action to search transactions
	 * 
	 * @return void
	 */
	public function actionSearch()
	{
		$params = array();
		
		// Get user model
		$model = new SearchTransactionForm("search");
		$model->scenario='search';

		if (!empty($_REQUEST['SearchTransactionForm'])) {
			$model->attributes = $_REQUEST['SearchTransactionForm'];

			$params['billing'] =  $model->billing;
			// Add search by id
			if (!empty($model->id)) {
				$params['id'] = $model->id;
			}
			
			// Add search by user id
			if (!empty($model->user_id)) {
				$params['user_id'] = $model->user_id;
				
				// Get user by id
				$user = User::model()->findByPk($model->user_id);
				
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
				'transactions' => Transaction::model()->search($params)
			)
		);
	}
}