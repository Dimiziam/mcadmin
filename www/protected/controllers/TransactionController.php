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
		$model = new User('search');
		
		if (!empty($_POST['User'])) {
			$model->attributes = $_POST['User'];
			
			// Add search by user id
			if (!empty($model->user_id)) {
				$params['user_id'] = $model->user_id;
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