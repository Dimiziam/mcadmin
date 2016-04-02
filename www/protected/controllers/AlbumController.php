<?php
/**
 * AlbumController
 *
 * Controller to work with albums
 *
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class AlbumController extends Controller {
	/**
	 * Specifies the access control rules.
	 *
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('autocomplete','setPermit','index','search','save'),
				'users' => array('@'),
				'roles'=>array('admin'),
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
		// Prepare criteria
		$criteria = new CDbCriteria;
		$criteria->condition = (!empty($_REQUEST['singer_id']) ? 'singer_id = :singerId and ' : '') . "title LIKE :title";
		$criteria->params = array(":title" => $_GET['q'] . "%");
		if (!empty($_REQUEST['singer_id'])) {
			$criteria->params[':singerId'] = (int) $_REQUEST['singer_id'];
		}

		// Find albums
		$albums = Album::model()->findAll($criteria);
		if (empty($albums)) {
			exit;
		}

		// Send albums
		$options = array();
		foreach ($albums as $album) {
			$options[] = array(
				'id' => $album->id,
				'label' => $album->title,
				'value' => $album->title,
			);
		}

		echo CJSON::encode($options);
		Yii::app()->end();
	}

	public function actionIndex() {
		$this->render('index',array('action'=>'search'));

	}

	/**
	 * methos to searc album
	 */
	public function actionSearch() {
		$album_id = Yii::app()->request->getParam('album');
		$search_id  = $album_id['id'] ? $album_id['id']: $album_id['id_manual'];


		$album_for_permit = Album::model()->findByPK($search_id);


		if (empty($album_for_permit)) {
			$this->render('index',array('action'=>'search'));
		}
		// Render view
		$this->render('edit',
			array(
				'albumsPermits' => $album_for_permit,
				'action' => 'save'
			)
		);

	}
	public function actionSave() {
		$params = Yii::app()->request->getParam('album');
		// for redirects
		$_POST['Album']['manual_id'] = $params['id'] ? $params['id'] : $params['id_manual'];

		if (!empty($params['search'])) {
			if (empty($params['name'])) {
				$params['id'] = '';
			}

			$this->actionSearch();
		} else {
			$album_params = Yii::app()->request->getParam('album');

			$search_id = !empty($album_params['id']) ? (int)$album_params['id'] : (int)!empty($album_params['id_manual']);
			$model = Album::model()->findByPk($search_id);
			if (!empty($model)) {
				$model->permit_from = (int)$album_params['permit_from'];

				if ($model->save()) {
					$this->actionSearch();
					//Yii::app()->end();
				}
			}
			else {
				$this->actionSearch();
			}

		}

		// save data

	}
}