<?php
/**
 * ChartController
 * 
 * Controller to work with charts
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class ChartController extends Controller {
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
	 * Displays a particular model.
	 * 
	 * @param integer $id the ID of the model to be displayed
	 * @return void
	 */
	public function actionAutocomplete()
	{
		// Prepare criteria
		$criteria = new CDbCriteria;
		$criteria->condition = "title LIKE :title";
		$criteria->params = array(":title" => $_GET['q'] . "%");
		$criteria->limit = $_GET['limit'];
		
		// Find chart sources
		$sources = Admin::model()->findAll($criteria);
		if (empty($sources)) {
			exit;
		}
		
		// Send sources
		foreach ($sources as $source) {
			echo $source->title . '|' . $source->id . "\n";
		}
		
		Yii::app()->end();
	}
	
	/**
	 * Action to delete chart source
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
			$result->message = MTLang::t('no_id', 'chart');
			$this->sendJson($result);
		}
		
		// Get chart source model
		$model = ChartSource::model()->findByPk($id);
		if (empty($model)) {
			$result->message = MTLang::t('not_found', 'chart');
			$this->sendJson($result);
		}
		
		// Delete
		if ($model->delete()) {
			$result->status = 'deleted';
		} else {
			$result->message = MTLang::t('message_1', 'chart');
		}
		
		$this->sendJson($result);
	}
	
	/**
	 * Action to add new chart source
	 * 
	 * @return void
	 */
	public function actionAdd()
	{
		$model = new ChartSource('create');

		if (isset($_POST['ChartSource'])) {
			$model->attributes = $_POST['ChartSource'];
			
			if ($model->save()) {
				$this->redirect(array('/chart/search/'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_2', 'chart'));
			}
		}

		$this->render('add', array('model' => $model));
	}
	
	/**
	 * Action to edit chart
	 * 
	 * @param integer $id
	 * @return void
	 */
	public function actionEdit($id)
	{
		$model = $this->loadModel($id);
		$model->scenario = 'update';
		
		if (isset($_POST['ChartSource'])) {
			// Set attributes
			$model->attributes = $_POST['ChartSource'];
			
			if ($model->save()) {
				$this->redirect(array('/chart/search/'));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_4', 'chart'));
			}
		}

		$this->render('edit', array('model' => $model));
	}
	
	/**
	 * Method return loaded model by id
	 * 
	 * @param integer the ID of the model to be loaded
	 * @return CActiveRecord
	 */
	public function loadModel($id)
	{
		$model = ChartSource::model()->findByPk($id);
		
		if ($model === null) {
			throw new CHttpException(404, MTLang::t('not_found', 'chart'));
		}
		
		return $model;
	}
	
	/**
	 * Action to search charts
	 * 
	 * @return void
	 */
	public function actionSearch()
	{
		$params = array();
		
		// Get user model
		$model = new SearchChartSourceForm('search');
		
		if (!empty($_REQUEST['SearchChartSourceForm'])) {
			$model->attributes = $_REQUEST['SearchChartSourceForm'];
			
			// Add search by id
			if (!empty($model->id)) {
				$params['id'] = $model->id;
				
				// Get chart source by id
				$source = Admin::model()->findByPk($model->id);
				
				// Set chart source title
				if (!empty($source)) {
					$model->title = $source->title;
				}
			}
		}
		
		// Render view
		$this->render('search', 
			array(
				'model' => $model,
				'charts' => ChartSource::model()->search($params)
			)
		);
	}
}