<?php
/**
 * StaticPageController
 * 
 * Controller to work with static pages
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class PageController extends Controller {
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('search', 'add', 'edit', 'delete'),
				'users' => array('@')
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * Action to delete user or mark it as disabled
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
			$result->message = MTLang::t('no_id', 'static_page');
			$this->sendJson($result);
		}
		
		// Get static page model
		$model = StaticPage::model()->findByPk($id);
		if (empty($model)) {
			$result->message = MTLang::t('not_found', 'user');
			$this->sendJson($result);
		}
		
		// Delete
		if ($model->delete()) {
			$result->status = 'deleted';
		} else {
			$result->message = MTLang::t('message_3', 'static_page');
		}
		
		$this->sendJson($result);
	}
	
	/**
	 * Action to add new user
	 * 
	 * @return void
	 */
	public function actionAdd($type)
	{
		// Get model
		$model = new StaticPage('create');
		$model->language = Yii::app()->language;

		// Get language model
		$languageModel = new StaticPageLanguage('create');
		
		if (isset($_POST['StaticPageLanguage'])) {
			$model->created = date('Y-m-d H:i:s', time());
			$model->type = $type;
			
			// Set language attributes
			$languageModel->attributes = $_POST['StaticPageLanguage'];
			
			// Create alias
			$model->alias = MTCommon::createAlias(trim($languageModel->title));
			
			if ($model->save()) {
				// Fill language model
				$languageModel->page_id = $model->id;
				$languageModel->language = Yii::app()->language;
				
				if ($languageModel->save()) {
					$this->redirect($this->createUrl('/page/search/', array('type' => $type)));
				} else {
					$model->delete();
					Yii::app()->user->setFlash('error', MTLang::t('message_2', 'static_page'));
				}
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_2', 'static_page'));
			}
		}

		$this->render('add', 
			array(
				'model' => $model, 
				'languageModel' => $languageModel,
				'language' => Yii::app()->language,
				'type' => $type,
			)
		);
	}
	
	/**
	 * Action to edit user
	 * 
	 * @param integer $id
	 * @return void
	 */
	public function actionEdit($lang, $type, $id)
	{
		$model = $this->loadModel($id);
		$model->scenario = 'update';
		$model->language = $lang;
		
		// Get language model
		$languageModel = StaticPageLanguage::model()->find(
			'page_id = :pageId and language = :language',
			array(
				':pageId' => $model->id,
				':language' => $lang
			)
		);
		if (empty($languageModel)) {
			$languageModel = new StaticPageLanguage('create');
		}
		
		if (isset($_POST['StaticPageLanguage'])) {
			// Set attributes
			$model->attributes = $_POST['StaticPage'];
			$model->type = $type;
			
			// Set language attributes
			$languageModel->attributes = $_POST['StaticPageLanguage'];
			$languageModel->page_id = $model->id;
			$languageModel->language = $lang;
			
			// Save
			if ($model->save() && $languageModel->save()) {
				$this->redirect($this->createUrl('/page/search/', array('type' => $type)));
			} else {
				Yii::app()->user->setFlash('error', MTLang::t('message_1', 'static_page'));
			}
		}

		$this->render('edit', 
			array(
				'model' => $model,
				'languageModel' => $languageModel,
				'language' => $lang,
				'type' => $type,
			)
		);
	}
	
	/**
	 * Method return loaded model by id
	 * 
	 * @param integer the ID of the model to be loaded
	 * @return CActiveRecord
	 */
	public function loadModel($id)
	{
		$model = StaticPage::model()->findByPk($id);
		
		if ($model === null) {
			throw new CHttpException(404, MTLang::t('not_found', 'user'));
		}
		
		return $model;
	}
	
	/**
	 * Action to search users
	 * 
	 * @param string $type
	 * @return void
	 */
	public function actionSearch($type)
	{
		$params = array(
			'language' => Yii::app()->language,
			'type' => $type
		);
		
		// Get user model
		$model = new SearchStaticPageForm('search');
		
		if (!empty($_REQUEST['SearchStaticPageForm'])) {
			$model->attributes = $_REQUEST['SearchStaticPageForm'];
			
			// Add search by type
			if (!empty($model->language)) {
				$params['language'] = $model->language;
			}
		}
		
		// Render view
		$this->render('search', 
			array(
				'model' => $model,
				'pages' => StaticPage::model()->search($params),
				'language' => $params['language'],
				'type' => $params['type'],
			)
		);
	}
}