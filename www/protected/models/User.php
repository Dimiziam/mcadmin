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
	 * Variable to store user id
	 * 
	 * @var int
	 */
	public $user_id;
	
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
			array('email, password', 'required', 'on' => 'create'),
			array('email', 'required', 'on' => 'update'),
			
			// Formats rules
			array('password', 'length', 'min' => 3, 'max' => 32, 'on' => 'create,update'),
			array('email', 'length', 'max' => 150, 'on' => 'create,update'),
			array('email', 'email', 'on' => 'create,update'),
			array('name, last_name', 'length', 'max' => 255, 'on' => 'create,update'),
			
			// Custom rules
			array('email', 'uniqueEmail', 'on' => 'create'),
			
			// Safe rule
			array('name, last_name, password, is_active, is_email_confirmed', 'safe', 'on' => 'create,update'),
			array('email, user_id', 'safe', 'on' => 'search'),
			
			// Default rules
			array('created', 'default', 'value' => date('Y-m-d H:i:s', time()), 'on' => 'create'),
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
			'name' => MTLang::t('name', 'user'),
			'last_name' => MTLang::t('last_name', 'user'),
			'is_active' => MTLang::t('is_active', 'user'),
			'is_email_confirmed' => MTLang::t('is_email_confirmed', 'user'),
		);
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
		if (!empty($item) && (!empty($this->id) || $item->id != $this->id)) {
			$this->addError('email', MTLang::t('email_exists', 'user'));
		}
	}
	
	/**
	 * After delete hook
	 * 
	 * @return void
	 */
	public function afterDelete()
	{
		// Remove all cache
		$this->cleareCache($this->id);
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
	
	/**
	 * Method to cleare current model cache for some user id
	 * 
	 * @param int $id
	 * @return bool
	 */
	public function cleareCache($id)
	{
		return TRedis::instance('users')->deleteByTag('USER_' . $id);
	}
	
	/**
	 * Method to search users
	 * 
	 * @param array $params
	 * @return mixed
	 */
	public function search($params)
	{
		// Create query command
		$command = $this->getDbConnection()->createCommand(
			'select SQL_CALC_FOUND_ROWS * 
			from ' . $this->tableName() . '
			' . (!empty($params['email']) ? 'where email = :email' : '') . '
			order by id desc
			limit ' . ($this->getCurrentPage() * $this->getRowsPerPage()) . ', ' . $this->getRowsPerPage()
		);
		
		// Bind params
		if (!empty($params['email'])) {
			$command->bindParam(":email", $params['email'], PDO::PARAM_STR);
		}
		
		// Try to find users
		$users = $command->queryAll();
		
		// Check users
		if (empty($users)) {
			return false;
		}
		
		// Get nuber of found rows
		$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
		
		// Get data provider
		$provider = new MTArrayDataProvider($users, 
			array(
				'totalItemCount' => $foundRows,
				'pagination' => array('pageSize' => $this->getRowsPerPage()),
			)
		);
		
		// Set offset
		$provider->setOffset(0);
		
		return $provider;
	}
}
