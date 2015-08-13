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
				'users' => array('@'),
				'roles'=>array('admin','seo'),
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

	/**
	 * action to edit singer seo
	 * @param $id
	 */
	public function actionEdit($id)
	{


		$model = SingerSeo::model()->findByPk($id);
		$model->scenario = 'update';

		if (isset($_POST['SingerSeo'])) {

			// Set attributes
			$model->attributes = $_POST['SingerSeo'];

			if ($model->save()) {
				$this->redirect(array('/singerSeo/search/'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_1', 'admin'));
			}
		}
		$this->render('edit', array('model' => $model));


	}

	/**
	 * Action to delete singerSeo inf
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
			$result->message = MTLang::t('no_id', 'seo');
			$this->sendJson($result);
		}

		// Get admin model
		$model = SingerSeo::model()->findByPk($id);
		if (empty($model)) {
			$result->message = MTLang::t('not_found', 'seo');
			$this->sendJson($result);
		}

		// Delete
		if ($model->delete()) {
			$result->status = 'deleted';
		} else {
			$result->message = MTLang::t('message_3', 'seo');
		}

		$this->sendJson($result);
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