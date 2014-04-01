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
	public $layout='//layouts/main';
	
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

		// Disable standart jquery 
		Yii::app()->clientScript->scriptMap = array('jquery.js' => false);

		// Get search string
		Yii::app()->params['search_string'] =  Yii::app()->request->getQuery('search_string', '');
		
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
	 * Method return temporary user token
	 * 
	 * @return mixed
	 */
	public function getTmpUserToken()
	{
		// Get cookie prefix
		$prefix = Yii::app()->params['cookies']['prefix'];
		
		// Try to get token from cookies
		if (!empty($_COOKIE[$prefix . 'tmpUserToken'])) {
			return $_COOKIE[$prefix . 'tmpUserToken'];
		}
		
		// Prepare params
		$expireTime = time() + Yii::app()->params['cookies']['tmp_token_life_time'];
		$path = '/';
		$domain = '.' . str_replace('www.', '', $_SERVER['SERVER_NAME']);
		$token = md5(uniqid(microtime() . rand(1, 100000)) . $_SERVER['REMOTE_ADDR']);
		
		// Set cookie
		setcookie($prefix . 'tmpUserToken', $token, $expireTime, $path, $domain);
		
		// Return token
		return $token;
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
	
	public function createMultilanguageReturnUrl($lang='en'){
		if (count($_GET)>0){
			$arr = $_GET;
			$arr['language']= $lang;
		}
		else
			$arr = array('language'=>$lang);
		return $this->createUrl('', $arr);
	}







	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('/error/error', $error);
		}
	}


/*
 * check user login or Guest
 */
	public  function check_loggetin() {
		// не авторизироване отслываем ерр
		if (Yii::app ()->user->isGuest) {
			$this->render ( '/error/error_user', array ('error_message' => 'user_not_loged_in' ) );
			exit();
		}

	}


	public function example_mailsend($name, $contact_no, $email,$website,$content,$subject,$address )
	{
		$message        = new YiiMailMessage;
		$message->view  = 'viewfilenm';
		$message->setBody(array(), 'text/html');
		$body           = $message->message->getBody();
		/******  preg_replace :Perform a regular expression search and replace ******/
		$body           = preg_replace('/\[FNAME]/',$name,$body);
		$body           = preg_replace('/\[CONTENT]/',$content,$body);
		$find           = array("[CONTACT_NO]"=>$contact_no,"[FNAME]"=>$name,"[EMAIL]"=>$email,"[ADDRESS]"=>$address);
		/****** strtr :Translate characters or replace substrings ******/
		$newstr         = strtr($content, $find);
		$body           = str_replace($content,$newstr,$body);
		$body           = preg_replace('/\[CONTACT_NO]/',$contact_no,$body);
		$body           = preg_replace('/\[EMAIL]/',$email,$body);
		$body           = preg_replace('/\[SUBJECT]/',$subject,$body);
		$body           = preg_replace('/\[WEBSITE]/',$website,$body);
		$message->message->setBody($body, 'text/html');
		$message->subject = $subject;
		$message->addTo($email);
		$message->from   = ('abc@abc.com');
		Yii::app()->mail->send($message);
	}

	/**
	 * Method to render view with cache
	 * 
	 * @param string $path - relative path to cache file
	 * @param string $view
	 * @param mixed $data
	 * @return void
	 */
	public function renderWithCache($pathTemplate, $pathParams, $view, $data = null)
	{
		// Render view
		$result = $this->render($view, $data, true);
		
		// Display result
		echo $result;
		
		// Cache result to file
		$this->_cacheToFile($pathTemplate, $pathParams, $result);
	}
	
	/**
	 * Method to render view partial with cache
	 * 
	 * @param string $path - relative path to cache file
	 * @param string $view
	 * @param mixed $data
	 * @return void
	 */
	public function renderPartialWithCache($pathTemplate, $pathParams, $view, $data = null)
	{
		// Render view
		$result = $this->renderPartial($view, $data, true);
		
		// Display result
		echo $result;
		
		// Cache result to file
		$this->_cacheToFile($pathTemplate, $pathParams, $result);
	}
	
	/**
	 * Method to cache render result to file
	 * 
	 * @param string $pathTemplate
	 * @param array $pathParams
	 * @param string $content
	 * @return bool
	 */
	private function _cacheToFile($pathTemplate, $pathParams, $content)
	{
		// Check is static cache enabled
		if (empty(Yii::app()->params['static_cache'])) {
			return true;
		}
		
		// Prepare full cache path
		$path = Yii::app()->params['paths']['static'] . '/' . str_replace(array_keys($pathParams), array_values($pathParams), $pathTemplate);
		
		// Try to create folders
		if (!file_exists(dirname($path)) && !mkdir(dirname($path), 0777, true)) {
			return null;
		}
		
		// Store result into file
		return file_put_contents($path, $content);
	}

}