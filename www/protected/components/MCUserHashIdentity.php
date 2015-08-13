<?php
/**
 * MCUserHashIdentity
 * 
 * Component to authenticate admin by hash
 * 
 * @package MediaClub
 * @subpackage components
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MCUserHashIdentity extends CUserIdentity
{
	const ERROR_HASH_EXPIRED = 300;
	
	/**
	 * Variable to store admin id
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
	 * Authenticates a admin
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		// Find admin by id
		$admin = Admin::model()->findByPk($this->_id);

		// Check admin
		if (empty($admin)) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else if (
			$this->_hash !== $admin->auth_hash && empty(Yii::app()->user->id)
		) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else if (time() > $admin->auth_hash_till && empty(Yii::app()->user->id)) {
			$this->errorCode = self::ERROR_HASH_EXPIRED;
		} else {
			// Store admin id
			$this->_id = $admin->id;

			// Set success status
			$this->errorCode = self::ERROR_NONE;
			
			// Rebuild authorization hash
			$this->rebuildHash($admin);
		}
		
		return !$this->errorCode;
	}
	
	/**
	 * Method to rebuild authorization hash
	 * 
	 * @param Admin $admin
	 * @return bool
	 */
	public function rebuildHash(Admin $admin)
	{
		// Check is need rebuild hash
		if (((time() - $admin->last_rebuild_hash) < Yii::app()->params['cookies']['hash_rebuild_period']) != 0) {
			return true;
		}
		
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
		
		// Change autho cookie
		$this->_setAuthCookie($admin);
		
		// Save admin
		return $admin->save();
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
	 * Method return admin id
	 * 
	 * @return int
	 */
	public function getId()
	{
		return $this->_id;
	}
}