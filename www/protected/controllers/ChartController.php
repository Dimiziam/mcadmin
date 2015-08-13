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
				'actions' => array(
					'search', 'add', 'edit', 'delete', 'autocomplete',
					'items', 'itemAdd', 'itemEdit', 'itemDelete'
				),
				'users' => array('@'),
				'roles'=>array('admin','seo'),
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * Action to delete chart item
	 * 
	 * @param int $id
	 * @return void
	 */
	public function actionItemDelete($id)
	{
		// Prepare result
		$result = new stdClass;
		$result->status = 'error';
		
		// Check id
		if (empty($id)) {
			$result->message = MTLang::t('no_id', 'chart');
			$this->sendJson($result);
		}
		
		// Get chart item model
		$model = ChartItem::model()->findByPk($id);
		if (empty($model)) {
			$result->message = MTLang::t('not_found', 'chart');
			$this->sendJson($result);
		}
		
		// Delete
		if ($model->delete()) {
			$result->status = 'deleted';
		} else {
			$result->message = MTLang::t('message_5', 'chart');
		}
		
		$this->sendJson($result);
	}
	
	/**
	 * Action to add new chart item
	 * 
	 * @param string $created
	 * @param int $sourceId
	 * @return void
	 */
	public function actionItemAdd($created, $sourceId)
	{
		$model = new ChartItem('create');

		if (isset($_POST['ChartItem'])) {
			$model->attributes = $_POST['ChartItem'];
			$model->source_id = (int) $sourceId;
			$model->created = addslashes($created);
			
			if ($model->save()) {
				$this->redirect(array('/chart/items/', 'id' => $model->source_id));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_6', 'chart'));
			}
		}

		$this->render('item_add', 
			array(
				'model' => $model,
				'chart' => ChartSource::model()->findByPk($sourceId)
			)
		);
	}
	
	/**
	 * Action to edit chart item
	 * 
	 * @param integer $id
	 * @return void
	 */
	public function actionItemEdit($id)
	{
		$model = $this->loadItemModel($id);
		$model->scenario = 'update';
		
		if (isset($_POST['ChartItem'])) {
			// Set attributes
			$model->attributes = $_POST['ChartItem'];
			
			if ($model->save()) {
				$this->redirect(array('/chart/items/', 'id' => $model->source_id));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_4', 'chart'));
			}
		}

		$this->render('item_edit', 
			array(
				'model' => $model,
				'chart' => ChartSource::model()->findByPk($model->source_id),
			)
		);
	}
	
	/**
	 * Method return loaded model by id
	 * 
	 * @param integer the ID of the model to be loaded
	 * @return CActiveRecord
	 */
	public function loadItemModel($id)
	{
		$model = ChartItem::model()->findByPk($id);
		
		if ($model === null) {
			throw new CHttpException(404, MTLang::t('item_not_found', 'chart'));
		}
		
		return $model;
	}
	
	/**
	 * Action to search chart items
	 * 
	 * @param int $id
	 * @return void
	 */
	public function actionItems($id)
	{
		$params = array();
		
		// Get user model
		$model = new SearchChartItemForm('search');
		
		// Add filter by current date
		$params['created'] = date('Y-m', time()) . '-01';
		
		if (!empty($_REQUEST['SearchChartItemForm'])) {
			$model->attributes = $_REQUEST['SearchChartItemForm'];
			
			// Add search by id
			if (!empty($model->year) && !empty($model->month)) {
				$params['created'] = $model->year . '-' . $model->month . '-01';
			}
		}

		// Refresh chart
		if (!empty($_REQUEST['need_refresh'])) {
			$this->_refresh($id, $params['created']);
		}
		
		// Render view
		$this->render('items', 
			array(
				'chart' => ChartSource::model()->findByPk($id),
				'model' => $model,
				'items' => ChartItem::model()->search($id, $params),
				'created' => $params['created']
			)
		);
	}
	
	/**
	 * Method to refresh chart
	 * 
	 * @param int $id
	 * @param string $created
	 * @return bool
	 */
	private function _refresh($id, $created)
	{
		// Remove old items
		if (!ChartItem::model()->deleteBySourceId($id, $created)) {
			Yii::app()->user->setFlash('error', MTLang::t('message_7', 'chart'));
			return false;
		}
		
		// Get chart items
		$items = MTCharts::getItems($id);
		if (empty($items)) {
			Yii::app()->user->setFlash('error', MTLang::t('message_8', 'chart'));
			return false;
		}
		
		// Add items
		if (!ChartItem::model()->bulckAdd($id, $created, $items)) {
			Yii::app()->user->setFlash('error', MTLang::t('message_9', 'chart'));
			return false;
		} else {
			Yii::app()->user->setFlash('success', MTLang::t('message_10', 'chart'));
			return true;
		}
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
	 * @param $id int integer the ID of the model to be loaded
	 * @throws CHttpException not_found
	 * @return CActiveRecord $model
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