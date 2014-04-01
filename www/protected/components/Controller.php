<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column2';
	
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	/**
	 * Class contructor
	 * 
	 * @param string $id
	 * @param mixed $module
	 * @return void
	 */
	public function __construct($id, $module = null){
		// Call parent method
		parent::__construct($id, $module);
		
		// Add meta tag with YII_CSRF_TOKEN
		Yii::app()->clientScript->registerMetaTag(Yii::app()->getRequest()->getCsrfToken(), Yii::app()->getRequest()->csrfTokenName);
	}
	
	/**
	 * Method to send JSON data
	 * 
	 * @param mixed $info
	 * @return void
	 */
	public function sendJson($info)
	{
		// Send header
		header('Content-type: application/json');
		
		// Compress and send JSON data
		echo function_exists('json_encode') ? json_encode($info) : CJSON::encode($info);
		
		// End app
		Yii::app()->end();
	}
	
	/**
	 * Method to authorize user by hash
	 * 
	 * @return void
	 */
	private function _authorizeUserByHash()
	{
		// Get cookie prefix
		$prefix = Yii::app()->params['cookies']['prefix'];
		
		if (!empty($_COOKIE[$prefix . 'userId']) && !empty($_COOKIE[$prefix . 'authHash'])) {
			// Get user identity
			$identity = new MCUserHashIdentity($_COOKIE[$prefix . 'userId'], $_COOKIE[$prefix . 'authHash']);
			
			// Authenticate
			if ($identity->authenticate()) {
				Yii::app()->user->login($identity);
			}
		}
	}

	/**
	 * Method to enable filters
	 * 
	 * @return array
	 */
	public function filters()
	{
		// Authorize user by hash
		$this->_authorizeUserByHash();
		
		return array('accessControl');
	}
}