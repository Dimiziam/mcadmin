<?php
/**
 * User
 * 
 * Model for table users
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class User extends InfoModel {
	/**
	 * Variable to store confirm password value
	 * 
	 * @var string
	 */
	public $confirmPassword;
	
	/**
	 * Variable to store current password value
	 * 
	 * @var string
	 */
	public $currentPassword;
	
	/**
	 * Variable to store ccaptcha string
	 * 
	 * @var string
	 */
	public $captcha;
	
	/**
	 * Returns the static model of the specified AR class.
	 * 
	 * @param string $className active record class name.
	 * @return Players the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Method return DB table name
	 * 
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * Method return field rules
	 * 
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// Required rules
			array('email, password, confirmPassword, captcha', 'required', 'on' => 'signup'),
			array('email, password', 'required', 'on' => 'signin'),
			array('login', 'required', 'on' => 'validateLogin'),
			array('email, captcha', 'required', 'on' => 'rememberPassword'),
			array('password, confirmPassword, captcha', 'required', 'on' => 'changePassword'),
			array('captcha', 'required', 'on' => 'profile'),
			
			// Formats rules
			array('password', 'length', 'min' => 3, 'max' => 32, 'on' => 'signup,signin,changePassword'),
			array('confirmPassword', 'compare', 'compareAttribute' => 'password', 'on' => 'signup,changePassword,profile'),
			array('email', 'length', 'max' => 150, 'on' => 'signup,rememberPassword'),
			array('email', 'email', 'on' => 'signup,rememberPassword'),
			array('name, last_name', 'length', 'max' => 255, 'on' => 'profile'),
			
			// Custom rules
			array('email', 'uniqueEmail', 'on' => 'signup'),
			array('email', 'existsEmail', 'on' => 'rememberPassword'),
			array('captcha', 'checkCaptcha', 'on' => 'signup,rememberPassword,profile'),
			array('currentPassword', 'checkCurrentPassword', 'on' => 'profile'),
			
			// Safe rule
			array('name, last_name, password, confirmPassword', 'safe', 'on' => 'profile'),
			
			// Default rules
			array('created', 'default', 'value' => date('Y-m-d H:i:s', time()), 'on' => 'signup'),
			array('approve_token_mail', 'default', 'value' => md5(uniqid(microtime() . rand(1, 1000000))), 'on' => 'signup'),
		);
	}

	/**
	 * Method return attribute labels
	 * 
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'email' => MTLang::t('email', 'user'),
			'password' => MTLang::t('password', 'user'),
			'confirmPassword' => MTLang::t('c_password', 'user'),
			'captcha' => MTLang::t('captcha', 'user'),
			'currentPassword' => MTLang::t('current_password', 'user'),
			'name' => MTLang::t('name', 'user'),
			'last_name' => MTLang::t('last_name', 'user'),
		);
	}
	
	/**
	 * Method to check current password
	 * 
	 * @param string $attribute
	 * @param array $params
	 */
	public function checkCurrentPassword($attribute, $params)
	{
		if (empty($this->currentPassword) && empty($this->password)) {
			return true;
		}
		
		// Try to find item by id
		$item = self::model()->findByPk($this->id);
		if (empty($item)) {
			$this->addError('currentPassword', MTLang::t('not_found', 'user'));
		}
		
		// Prepare encrypted password
		$password = md5(
			Yii::app()->params['secret_key'] 
			. md5($item->secret_key . $this->currentPassword . Yii::app()->params['secret_key'])
			. $item->secret_key
		);
		
		// Check is item exists
		if ($item->password != $password) {
			$this->addError('currentPassword', MTLang::t('current_password_incorrect', 'user'));
		}
	}
	
	/**
	 * Method to check captcha string
	 * 
	 * @param string $attribute
	 * @param array $params
	 */
	public function checkCaptcha($attribute, $params)
	{
		// Get captcha handler
		$captcha = new MTCaptcha('data', Yii::app()->getController()->getTmpUserToken());
		
		// Configure captcha
		$captcha->configure(array('key' => 'user'));
		
		// Validate captcha string
		if (!$captcha->validate($this->captcha)) {
			$this->addError('captcha', MTLang::t('incorrect_captcha', 'user'));
		}
	}
	
	/**
	 * Method to check is email exists
	 * 
	 * @param string $attribute
	 * @param array $params
	 */
	public function existsEmail($attribute, $params)
	{
		// Try to find item by email
		$item = self::model()->find('email = :email', array(':email' => $this->email));
		
		// Check is item exists
		if (empty($item)) {
			$this->addError('email', MTLang::t('email_not_exists', 'user'));
		}
	}
	
	/**
	 * Method to check is email unique
	 * 
	 * @param string $attribute
	 * @param array $params
	 */
	public function uniqueEmail($attribute, $params)
	{
		// Try to find item by email
		$item = self::model()->find('email = :email', array(':email' => $this->email));
		
		// Check is item exists
		if (!empty($item)) {
			$this->addError('email', MTLang::t('email_exists', 'user'));
		}
	}
	
	/**
	 * Method to cleare current model cache for some user id
	 * 
	 * @param int $id
	 * @return bool
	 */
	public function cleareCache($id)
	{
		return TRedis::instance('users')->deleteByTag('USER_' . $id)
			&& MTCache::instance('models')->delete('user_' . $id);
	}
	
	/**
	 * Method return user info by id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id)
	{
		// Prepare cache key
		$key = 'user_' . $id;
		
		// Check app cache
		if (!MTCache::instance('models')->test($key)) {
			// Try to get user from cache
			$user = TRedis::instance('users')->get($key);
			if (!empty($user)) {
				// Store into app cache
				MTCache::instance('models')->set($key, $user);
			}
		} else {
			// Get user from app cache
			$user = MTCache::instance('models')->get($key);
		}
		
		if (empty($user)) {
			// Get user from DB
			$user = $this->getDbConnection()
				->createCommand('select * from ' . $this->tableName() . ' where id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryRow();
			if (empty($user)) {
				return false;
			}
			
			// Store user into cache
			TRedis::instance('users')->setByTag('USER_' . $id, $key, $user);
			
			// Store into app cache
			MTCache::instance('models')->set($key, $user);
		}
		
		return $user;
	}
	
	/**
	 * Method to save model
	 * 
	 * @param bool $runValidation
	 * @param array $attributes
	 */
	public function save($runValidation = true, $attributes = null)
	{
		// Call parent method
		$result = parent::save($runValidation, $attributes);
		
		// Check result
		if ($result === true && !empty($this->id)) {
			// Remove all cache
			$this->cleareCache($this->id);
		}
		
		return $result;
	}
}
