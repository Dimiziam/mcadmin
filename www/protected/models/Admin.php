<?php
/**
 * Admin
 * 
 * Model for table admins
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Admin extends InfoModel {
	
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
		return 'admins';
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
			array('login, password', 'required', 'on' => 'create,update'),
			
			// Formats rules
			array('password', 'length', 'min' => 3, 'max' => 32, 'on' => 'create,update'),
			array('login', 'length', 'max' => 150, 'on' => 'create,update'),
			
			// Custom rules
			array('login', 'uniqueLogin', 'on' => 'create,update'),
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
			'login' => MTLang::t('login', 'admin'),
			'password' => MTLang::t('password', 'admin'),
		);
	}
	
	/**
	 * Method to check is login unique
	 * 
	 * @param string $attribute
	 * @param array $params
	 */
	public function uniqueLogin($attribute, $params)
	{
		// Try to find item by login
		$item = self::model()->find('login = :login', array(':login' => $this->login));
		
		// Check is item exists
		if (!empty($item) && $item->id != $this->id) {
			$this->addError('login', MTLang::t('login_exists', 'admin'));
		}
	}
	
	/**
	 * Method to cleare current model cache for some admin id
	 * 
	 * @param int $id
	 * @return void
	 */
	public function cleareCache($id)
	{
		TRedis::instance('data')->deleteByTag('ADMIN_' . $id);
		MTCache::instance('models')->delete('admin_' . $id);
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
		$key = 'admin_' . $id;
		
		// Check app cache
		if (!MTCache::instance('models')->test($key)) {
			// Try to get admin from cache
			$admin = TRedis::instance('data')->get($key);
			if (!empty($admin)) {
				// Store into app cache
				MTCache::instance('models')->set($key, $admin);
			}
		} else {
			// Get user from app cache
			$user = MTCache::instance('models')->get($key);
		}
		
		if (empty($admin)) {
			// Get admin from DB
			$admin = $this->getDbConnection()
				->createCommand('select * from ' . $this->tableName() . ' where id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryRow();
			if (empty($admin)) {
				return false;
			}
			
			// Store user into cache
			TRedis::instance('data')->setByTag('ADMIN_' . $id, $key, $admin);
			
			// Store into app cache
			MTCache::instance('models')->set($key, $admin);
		}
		
		return $admin;
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
