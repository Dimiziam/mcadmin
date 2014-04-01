<?php
/**
 * Transaction
 * 
 * Model for table transactions
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Transaction extends InfoModel
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
		return 'transactions';
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
	 * Method return list of all transactions by user id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByUserId($id)
	{
		// Prepare cache key
		$key = 'transactions_' . $id;
		
		// Check app cache
		if (!MTCache::instance($key)->test('all')) {
			// Get list of transactions from cache
			$transactions = TRedis::instance('data')->get($key);
			if (!empty($transactions)) {
				// Store into app cache
				MTCache::instance($key)->set('all', $transactions);
				
				// Return transactions
				return $transactions;
			}
		} else {
			// Get transactions from app cache
			return MTCache::instance($key)->get('all');
		}
		
		// Get list of transactions from DB
		$transactions = $this->getDbConnection()
			->createCommand(
				'select * 
				from ' . $this->tableName() . '
				where user_id = :userId
				order by date_start desc'
			)
			->bindParam(':userId', $id, PDO::PARAM_INT)
			->queryAll();
		if (empty($transactions)) {
			return false;
		}

		// Prepare transactions
		foreach ($transactions as $index => $transaction) {
			// Unserialize info
			$transaction['info'] = !empty($transaction['info']) ? unserialize($transaction['info']) : null;
			
			$transactions[$index] = $transaction;
		}
		
		// Store list of transactions
		TRedis::instance('data')->setByTag('TRANSACTIONS_' . $id, $key, $transactions);
		
		// Store list of transactions into app cache
		MTCache::instance($key)->set('all', $transactions);
		
		return $transactions;
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @param int $userId
	 * @return bool
	 */
	public function cleareCache($userId)
	{
		return TRedis::instance('data')->deleteByTag('TRANSACTIONS_' . $userId)
			&& MTCache::instance('transactions_' . $userId)->cleareCache()
			&& User::model()->cleareCache($userId);
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