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
			pc::debug($model->attributes);
			if ($model->save()) {
				$this->redirect($this->createUrl('/singerSeo/search/'));
				Yii::app()->end();
			}
		}
		$this->render('add', array('model' => $model));

	}


	public function actionEdit($id)
	{
		echo 123;
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
		//pc::debug($_REQUEST['SingerSeo']);
		if (!empty($_REQUEST['SearchSingerSeoForm'])) {
			$model->attributes = $_REQUEST['SearchSingerSeoForm'];
			$params['singer_id'] = $model->singer_id;

			/*
			// Add search by id
			if (!empty($model->singer_id)) {
				$params['singer_id'] = $model->singer_id;
				// Get singer by id
				$singerSeo = SingerSeo::model()->findByAttributes(array('singer_id' => $model->singer_id));
				pc::debug($singerSeo);
			}
		*/
		}

		// Render view
		$this->render('search',
			array(
				'model' => $model,
				'singersSeo' => SingerSeo::model()->search($params)
			)
		);
	}


}