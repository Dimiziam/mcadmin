<?php
/**
 * OrderController
 * 
 * Controller to work with orders
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class OrderController extends Controller {
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('search', 'songs'),
				'users' => array('@')
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * Action to display order songs
	 * 
	 * @param int $id
	 * @return void
	 */
	public function actionSongs($id)
	{
		// Render view
		$this->render('songs', 
			array(
				'songs' => OrderSong::model()->getByOrderId($id),
				'orderId' => $id
			)
		);
	}
	
	/**
	 * Action to search orders
	 * 
	 * @return void
	 */
	public function actionSearch()
	{
		$params = array();
		
		// Get user model
		$model = new SearchOrderForm('search');
		
		if (!empty($_REQUEST['SearchOrderForm'])) {
			$model->attributes = $_POST['SearchOrderForm'];
			
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
				'orders' => Order::model()->search($params)
			)
		);
	}
}