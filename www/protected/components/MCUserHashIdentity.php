<?php
/**
 * MCUserHashIdentity
 * 
 * Component to authenticate user by hash
 * 
 * @package MediaClub
 * @subpackage components
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MCUserHashIdentity extends CUserIdentity
{
	const ERROR_USER_NOT_CONFIRM_EMAIL = 200;
	const ERROR_HASH_EXPIRED = 300;
	
	/**
	 * Variable to store user id
	 * 
	 * @var int
	 */
	private $_id;
	
	/**
	 * Variable to store authorization hash
	 * 
	 * @var string
	 */
	private $_hash;
	
	/**
	 * Constructor
	 * 
	 * @param int $id
	 * @param string $hash
	 * @return void
	 */
	public function __construct($id, $hash)
	{
		$this->_id = (int) $id;
		$this->_hash = $hash;
	}
	
	/**
	 * Authenticates a user
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		// Find user by id
		$user = User::model()->findByPk($this->_id);
		
		// Check $user
		if (empty($user)) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else if (
			$this->_hash !== $user->auth_hash && empty(Yii::app()->user->id)
		) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else if (time() > $user->auth_hash_till && empty(Yii::app()->user->id)) {
			$this->errorCode = self::ERROR_HASH_EXPIRED;
		} else if ($user->is_email_confirmed != 'yes') {
			$this->errorCode = self::ERROR_USER_NOT_CONFIRM_EMAIL;
		} else {
			// Store user id
			$this->_id = $user->id;
			
			// Set success status
			$this->errorCode = self::ERROR_NONE;
			
			// Rebuild authorization hash
			$this->rebuildHash($user);
		}
		
		return !$this->errorCode;
	}
	
	/**
	 * Method to rebuild authorization hash
	 * 
	 * @param User $user
	 * @return bool
	 */
	public function rebuildHash(User $user)
	{
		// Check is need rebuild hash
		if (((time() - $user->last_rebuild_hash) < Yii::app()->params['cookies']['hash_rebuild_period']) != 0) {
			return true;
		}
		
		// Calculate hash expire time
		$user->auth_hash_till = time() + Yii::app()->params['cookies']['life_time'];
		
		// Create hash
		$user->auth_hash = md5(
			Yii::app()->params['secret_key'] 
			. md5($user->secret_key . $user->auth_hash_till . Yii::app()->params['secret_key'])
			. $user->secret_key
		);
		
		// Set rebuild hash time
		$user->last_rebuild_hash = time();
		
		// Store user auth params
		$this->setState('auth_hash', $user->auth_hash);
		$this->setState('auth_hash_till', $user->auth_hash_till);
		
		// Change autho cookie
		$this->_setAuthCookie($user);
		
		// Save user
		return $user->save();
	}
	
	/**
	 * Method to set auth cookie
	 * 
	 * @param User $user
	 * @return void
	 */
	private function _setAuthCookie(User $user)
	{
		// Prepare params
		$expireTime = time() + Yii::app()->params['cookies']['life_time'];
		$path = '/';
		$domain = '.' . str_replace('www.', '', $_SERVER['SERVER_NAME']);
		$prefix = Yii::app()->params['cookies']['prefix'];
		
		// Set cookie
		setcookie($prefix . 'userId', $user->id, $expireTime, $path, $domain);
		setcookie($prefix . 'authHash', $user->auth_hash, $expireTime, $path, $domain);
	}
	
	/**
	 * Method return user id
	 * 
	 * @return int
	 */
	public function getId()
	{
		return $this->_id;
	}
}