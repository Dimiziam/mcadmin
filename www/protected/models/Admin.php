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
			array('login, password', 'required', 'on' => 'create,signin'),
			array('login', 'required', 'on' => 'update'),
			
			// Formats rules
			array('password', 'length', 'min' => 3, 'max' => 32, 'on' => 'create,update'),
			array('login', 'length', 'max' => 150, 'on' => 'create,update'),
			
			// Safe rule
			array('login', 'safe', 'on' => 'search'),
			
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
		if (!empty($item) && (!empty($this->id) || $item->id != $this->id)) {
			$this->addError('login', MTLang::t('login_exists', 'admin'));
		}
	}
	
	/**
	 * Method return user info by id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id)
	{
		// Get admin from DB
		$admin = $this->getDbConnection()
			->createCommand('select * from ' . $this->tableName() . ' where id = :id')
			->bindParam(":id", $id, PDO::PARAM_INT)
			->queryRow();
		if (empty($admin)) {
			return false;
		}
		
		return $admin;
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
			' . (!empty($params['id']) ? 'where id = :id' : '') . '
			order by id desc
			limit ' . ($this->getCurrentPage() * $this->getRowsPerPage()) . ', ' . $this->getRowsPerPage()
		);
		
		// Bind params
		if (!empty($params['id'])) {
			$command->bindParam(":id", $params['id'], PDO::PARAM_INT);
		}
		
		// Try to find admins
		$admins = $command->queryAll();
		
		// Check users
		if (empty($admins)) {
			return false;
		}
		
		// Get nuber of found rows
		$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
		
		// Get data provider
		$provider = new MTArrayDataProvider($admins, 
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
