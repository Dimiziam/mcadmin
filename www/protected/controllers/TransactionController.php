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
				'actions' => array('search'),
				'users' => array('@')
			),
			array('deny',
				'users' => array('*'),
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
		$model = new SearchTransactionForm('search');
		
		if (!empty($_REQUEST['SearchTransactionForm'])) {
			$model->attributes = $_POST['SearchTransactionForm'];
			
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