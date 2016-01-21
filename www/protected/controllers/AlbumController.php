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
				'actions' => array('autocomplete','permit'),
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

	public function actionPermit() {



	}
}