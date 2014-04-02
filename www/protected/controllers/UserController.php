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
				'actions' => array('search'),
				'users' => array('@')
			),
			array('deny',
				'users' => array('*'),
			)
		);
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
		$model = new User('search');
		
		if (!empty($_POST['User'])) {
			$model->attributes = $_POST['User'];
			
			// Add search by email
			if (!empty($model->email)) {
				$params['email'] = $model->email;
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
}