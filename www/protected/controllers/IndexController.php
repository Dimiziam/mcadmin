<?php
/**
 * IndexController
 * 
 * Controller for index page
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class IndexController extends Controller 
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
				'actions' => array('index'),
				'users' => array('@'),
				'roles'=>array('admin','seo'),

			),
			array('allow',
				'actions' => array('error'),
				'users' => array('*'),


			),

			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * Action to display index page
	 * 
	 * @return void
	 */
	public function actionIndex()
	{
		$this->render('index');
	}


	public function actionError()
	{


		if($error=Yii::app()->errorHandler->error) {

			$this->render('index', array("error"=>$error));

		}

	}

}

