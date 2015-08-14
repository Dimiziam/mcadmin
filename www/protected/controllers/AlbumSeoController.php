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
class AlbumSeoController extends Controller
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
				'roles' => array('admin', 'seo'),
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * method to autocomlite album by name
	 */
	public function actionAutocomplete()
	{

		//if (Yii::app()->request->isAjaxRequest && isset($_GET['q'])) {

		$albums = Album::model()->search($_GET['q'], (int)$_GET['limit']);

		if (empty($albums)) {
			exit;
		}
		// Send albums
		foreach ($albums as $album) {
			echo $album['title'] . ' - ' . $album['sname'] . '|' . $album['id'] . "\n";

		}

//		}

		Yii::app()->end();
	}

	/**
	 * method to add ned seo info
	 */
	public function actionAdd()
	{
		$model = new AlbumSeo('create');

		if (isset($_POST['AlbumSeo'])) {
			$model->attributes = $_POST['AlbumSeo'];
			if ($model->save()) {
				$this->redirect($this->createUrl('/albumSeo/search/'));
				Yii::app()->end();
			}
		}
		$this->render('add', array('model' => $model));

	}

	/**
	 * action to edit album seo
	 * @param $id
	 */
	public function actionEdit($id)
	{


		$model = AlbumSeo::model()->findByPk($id);
		$model->scenario = 'update';

		if (isset($_POST['AlbumSeo'])) {

			// Set attributes
			$model->attributes = $_POST['AlbumSeo'];

			if ($model->save()) {
				$this->redirect(array('/albumSeo/search/'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_1', 'admin'));
			}
		}
		$this->render('edit', array('model' => $model));


	}

	/**
	 * Action to delete albumSeo inf
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
		$model = AlbumSeo::model()->findByPk($id);
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
		$model = new AlbumSeo('search');
		//pc::debug($_REQUEST['SingerSeo']);
		if (!empty($_REQUEST['SearchAlbumSeoForm'])) {
			$model->attributes = $_REQUEST['SearchAlbumSeoForm'];
			$params['album_id'] = $model->album_id;

		}

		// Render view
		$this->render('search',
			array(
				'model' => $model,
				'albumsSeo' => AlbumSeo::model()->search($params)
			)
		);
	}


}