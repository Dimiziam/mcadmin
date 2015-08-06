<?php

/**
 * SeoController
 *
 * Controller to work with seo headers and text
 *
 * @package MediaClub
 * @subpackage controllers
 * @copyright
 * @author Roman Shtempeliuk
 */
class SingerSeoController extends Controller
{
	/**
	 * Specifies the access control rules.
	 *
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('search', 'add', 'edit', 'delete', 'autocomplete'),
				'users' => array('@')
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * method to autocomlite singer by name
	 */
	public function actionAutocomplete()
	{

		//if (Yii::app()->request->isAjaxRequest && isset($_GET['q'])) {
		// Prepare criteria
		$criteria = new CDbCriteria;

		$criteria->condition = "`name` LIKE :name";
		$criteria->limit = $_GET['limit'];

		$criteria->params = array(":name" => "%" . $_GET['q'] . "%");
		$criteria->limit = $_GET['limit'];

		// Find singers
		$singers = Singer::model()->findAll($criteria);

		if (empty($singers)) {
			exit;
		}
		// Send singer
		foreach ($singers as $singer) {
			echo $singer->name . '|' . $singer->id . "\n";
		}

//		}

		Yii::app()->end();
	}

	/**
	 * method to add ned seo info
	 */
	public function actionAdd()
	{
		$model = new SingerSeo('create');

		if (isset($_POST['SingerSeo'])) {
			$model->attributes = $_POST['SingerSeo'];
			if ($model->save()) {
				//$this->redirect($this->createUrl('/singerSeo/search/'));
				Yii::app()->end();
			}
		}
		$this->render('add', array('model' => $model));

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
		$model = new SingerSeo('search');
/*
		if (!empty($_REQUEST['SingerSeo'])) {
			$model->attributes = $_REQUEST['SingerSeo'];

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
*/
		// Render view
		$this->render('search',
			array(
				'model' => $model,
				'singersSeo' => SingerSeo::model()->search($params)
			)
		);
	}


}