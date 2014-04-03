<?php
/**
 * Order
 * 
 * Model for table orders
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Order extends InfoModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * 
	 * @param string $className active record class name.
	 * @return Resource the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Method return table name for current model
	 * 
	 * @return string
	 */
	public function tableName()
	{
		return 'orders';
	}
	
	/**
	 * Returns the primary key of the associated database table.
	 * 
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}
	
	/**
	 * Method return number of orders by user id
	 * 
	 * @param int $id
	 * @return int
	 */
	public function getNumberByuserId($id)
	{
		return (int) $this->getDbConnection()
			->createCommand(
				'select count(*) 
				from ' . $this->tableName() . '
				where user_id = :userId'
			)
			->bindParam(':userId', $id, PDO::PARAM_INT)
			->queryScalar();
	}
	
	/**
	 * Method return list of all orders by user id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByUserId($id)
	{
		// Get list of orders from DB
		$orders = $this->getDbConnection()
			->createCommand(
				'select * 
				from ' . $this->tableName() . '
				where user_id = :userId
				order by created desc'
			)
			->bindParam(':userId', $id, PDO::PARAM_INT)
			->queryAll();
		if (empty($orders)) {
			return false;
		}
		
		return $orders;
	}
	
	/**
	 * Method to search orders
	 * 
	 * @param array $params
	 * @return mixed
	 */
	public function search($params)
	{
		// Create query command
		$command = $this->getDbConnection()->createCommand(
			'select SQL_CALC_FOUND_ROWS orders.*, users.email, transactions.status
			from ' . $this->tableName() . ' orders
				inner join ' . User::model()->tableName() . ' users on users.id = orders.user_id
				inner join ' . Transaction::model()->tableName() . ' transactions on transactions.id = orders.transaction_id
			' . (!empty($params['user_id']) ? 'where orders.user_id = :user_id' : '') . '
			order by orders.id desc
			limit ' . ($this->getCurrentPage() * $this->getRowsPerPage()) . ', ' . $this->getRowsPerPage()
		);
		
		// Bind params
		if (!empty($params['user_id'])) {
			$command->bindParam(":user_id", $params['user_id'], PDO::PARAM_STR);
		}
		
		// Try to find orders
		$orders = $command->queryAll();
		
		// Check orders
		if (empty($orders)) {
			return false;
		}
		
		// Get nuber of found rows
		$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
		
		// Get data provider
		$provider = new MTArrayDataProvider($orders, 
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