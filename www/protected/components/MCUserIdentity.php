<?php
/**
 * MCUserIdentity
 * 
 * Component to authenticate admin by login and password
 * 
 * @package MediaClub
 * @subpackage components
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MCUserIdentity extends CUserIdentity
{
	/**
	 * Variable to store admin id
	 * 
	 * @var int
	 */
	private $_id;
	
	/**
	 * Authenticates a admin
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		// Fid admin by email
		$admin = Admin::model()->findByAttributes(array('login' => $this->username));
		
		// Check admin
		if (empty($admin)) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else if (
			$admin->password !== md5(
				Yii::app()->params['secret_key'] 
				. md5($admin->secret_key . $this->password . Yii::app()->params['secret_key'])
				. $admin->secret_key
			)
		) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else {
			// Store admin id
			$this->_id = $admin->id;
			
			// Set success status
			$this->errorCode = self::ERROR_NONE;
			
			// Build authorization hash
			if ($this->_buildHash($admin)) {
				// Set auth cookie
				$this->_setAuthCookie($admin);
			}
		}
		
		return !$this->errorCode;
	}
	
	/**
	 * Method to set auth cookie
	 * 
	 * @param Admin $admin
	 * @return void
	 */
	private function _setAuthCookie(Admin $admin)
	{
		// Prepare params
		$expireTime = time() + Yii::app()->params['cookies']['life_time'];
		$path = '/';
		$domain = '.' . str_replace('www.', '', $_SERVER['SERVER_NAME']);
		$prefix = Yii::app()->params['cookies']['prefix'];
		
		// Set cookie
		setcookie($prefix . 'userId', $admin->id, $expireTime, $path, $domain);
		setcookie($prefix . 'authHash', $admin->auth_hash, $expireTime, $path, $domain);
	}
	
	/**
	 * Method to build authorization hash
	 * 
	 * @param Admin $admin
	 * @return bool
	 */
	private function _buildHash(Admin $admin)
	{
		// Calculate hash expire time
		$admin->auth_hash_till = time() + Yii::app()->params['cookies']['life_time'];
		
		// Create hash
		$admin->auth_hash = md5(
			Yii::app()->params['secret_key'] 
			. md5($admin->secret_key . $admin->auth_hash_till . Yii::app()->params['secret_key'])
			. $admin->secret_key
		);
		
		// Set rebuild hash time
		$admin->last_rebuild_hash = time();
		
		// Store admin auth params
		$this->setState('auth_hash', $admin->auth_hash);
		$this->setState('auth_hash_till', $admin->auth_hash_till);
		
		// Save admin
		return $admin->save();
	}
	
	/**
	 * Method return admin id
	 * 
	 * @return int
	 */
	public function getId()
	{
		return $this->_id;
	}
}