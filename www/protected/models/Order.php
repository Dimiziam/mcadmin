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
}