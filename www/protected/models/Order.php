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
	 * Method return list of all orders by user id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByUserId($id)
	{
		// Prepare kache key
		$key = 'orders_' . $id;
		
		// Check app cache
		if (!MTCache::instance($key)->test('all')) {
			// Get list of orders from cache
			$orders = TRedis::instance('data')->get($key);
			if (!empty($orders)) {
				// Store into app cache
				MTCache::instance($key)->set('all', $orders);
				
				// Return orders
				return $orders;
			}
		} else {
			// Get orders from app cache
			return MTCache::instance($key)->get('all');
		}
		
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

		// Store list of orders
		TRedis::instance('data')->setByTag('ORDERS_' . $id, $key, $orders);
		
		// Store list of orders into app cache
		MTCache::instance($key)->set('all', $orders);
		
		return $orders;
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @param int $userId
	 * @return bool
	 */
	public function cleareCache($userId)
	{
		return TRedis::instance('data')->deleteByTag('ORDERS_' . $userId)
			&& MTCache::instance('orders_' . $userId)->cleareCache();
	}
	
	/**
	 * After delete hook
	 * 
	 * @return void
	 */
	public function afterDelete()
	{
		// Remove all cache
		$this->cleareCache($this->user_id);
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
		if ($result === true) {
			// Remove all cache
			$this->cleareCache($this->user_id);
		}
		
		return $result;
	}
}