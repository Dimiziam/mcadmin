<?php
/**
 * MCUserIdentity
 * 
 * Component to authenticate user by email and password
 * 
 * @package MediaClub
 * @subpackage components
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MCUserIdentity extends CUserIdentity
{
	const ERROR_USER_NOT_CONFIRM_EMAIL = 200;
	
	/**
	 * Variable to store user id
	 * 
	 * @var int
	 */
	private $_id;
	
	/**
	 * Authenticates a user
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		// Fid user by email
		$user = User::model()->findByAttributes(array('email' => $this->username));
		
		// Check $user
		if (empty($user)) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else if (
			$user->password !== md5(
				Yii::app()->params['secret_key'] 
				. md5($user->secret_key . $this->password . Yii::app()->params['secret_key'])
				. $user->secret_key
			)
		) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else if ($user->is_email_confirmed != 'yes') {
			$this->errorCode = self::ERROR_USER_NOT_CONFIRM_EMAIL;
		} else {
			// Store user id
			$this->_id = $user->id;
			
			// Set success status
			$this->errorCode = self::ERROR_NONE;
			
			// Build authorization hash
			if ($this->_buildHash($user)) {
				// Set auth cookie
				$this->_setAuthCookie($user);
			}
		}
		
		return !$this->errorCode;
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
	 * Method to build authorization hash
	 * 
	 * @param User $user
	 * @return bool
	 */
	private function _buildHash(User $user)
	{
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
		
		// Save user
		return $user->save();
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