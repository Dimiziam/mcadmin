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
//	public function accessRules()
//	{
//		return array(
//			array('allow',
//				'actions' => array('search', 'add', 'edit', 'delete'),
//				'users' => array('@')
//			),
//			array('deny',
//				'users' => array('*'),
//			)
//		);
//	}

	/**
	 * method to autocomlite singer by name
	 */
	public function actionAutocomplete()
	{

		//if (Yii::app()->request->isAjaxRequest && isset($_GET['q'])) {
		$_GET['q']='abb';
			// Prepare criteria
			$criteria = new CDbCriteria;

			$criteria->condition = "`name` LIKE :name";
			$criteria->limit = $_GET['limit'];

			$criteria->params = array(":name" => $_GET['q'] . "%");
			//$criteria->limit = $_GET['limit'];

			// Find users
			$singers = Singer::model()->findAll($criteria);


			if (empty($singer)) {
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
		$this->render('add', array('model' => $model));

	}


}