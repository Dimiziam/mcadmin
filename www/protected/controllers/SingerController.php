<?php
/**
 * SingerController
 * 
 * Controller to work with singers
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SingerController extends Controller {
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('autocomplete'),
				'users' => array('@')
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
		$criteria->condition = "name LIKE :name or pseudonym LIKE :name";
		$criteria->params = array(":name" => $_GET['q'] . "%");
		$criteria->limit = $_GET['limit'];
		
		// Find singers
		$singers = Singer::model()->findAll($criteria);
		if (empty($singers)) {
			exit;
		}
		
		// Send singers
		foreach ($singers as $singer) {
			echo $singer->name . (!empty($singer->pseudonym) ? ' (' .$singer->pseudonym . ')' : '') . '|' . $singer->id . "\n";
		}
		
		Yii::app()->end();
	}
}