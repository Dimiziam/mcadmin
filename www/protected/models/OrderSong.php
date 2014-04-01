<?php
/**
 * OrderSong
 * 
 * Model for table order_songs
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class OrderSong extends InfoModel
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
		return 'order_songs';
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
	 * Method return list of all songs by order id
	 * 
	 * @param int $id
	 * @param int $userId
	 * @return mixed
	 */
	public function getByOrderId($id, $userId = 0)
	{
		// Prepare cache key
		$key = 'order_songs' . $id;
		
		// Check app cache
		if (!MTCache::instance('models')->test($key)) {
			// Get list of songs from cache
			$songs = TRedis::instance('data')->get($key);
			if (!empty($songs)) {
				// Store into app cache
				MTCache::instance('models')->set($key, $songs);
				
				// Return songs
				return $songs;
			}
		} else {
			// Get songs from app cache
			return MTCache::instance('models')->get($key);
		}
		
		// Prepare DB command
		$command = $this->getDbConnection()
			->createCommand(
				'select * 
				from ' . $this->tableName() . '
				where order_id = :orderId'
				. (!empty($userId) ? ' and user_id = :userId' : '')
			);
		
		// Bind params
		$command->bindParam(':orderId', $id, PDO::PARAM_INT);
		if (!empty($userId)) { 
			$command->bindParam(':userId', $userId, PDO::PARAM_INT);
		}
			
		// Get list of songs from DB
		$songs = $command->queryAll();
		if (empty($songs)) {
			return false;
		}

		// Prepare songs
		$prepared = array();
		foreach ($songs as $song) {
			$prepared[$song['song_id']] = $song;
		}
		
		// Store list of songs
		TRedis::instance('data')->set($key, $prepared);
		
		// Store list of songs into app cache
		MTCache::instance('models')->set($key, $prepared);
		
		return $prepared;
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @param int $orderId
	 * @return bool
	 */
	public function cleareCache($orderId)
	{
		return TRedis::instance('data')->delete('order_songs' . $orderId)
			&& MTCache::instance('models')->delete('order_songs' . $orderId);
	}
	
	/**
	 * Method to store songs into order
	 * 
	 * @param array $songs
	 * @return bool
	 */
	public function storeSongs($songs)
	{
		if (empty($songs)) {
			return false;
		}
		
		// Get fields
		$fields = array_keys($songs[0]);
		
		// Prepare records
		$records = array();
		foreach ($songs as $song) {
			$record = array();
			
			// Process fields
			foreach ($song as $field => $value) {
				$record[] = '"' . addslashes($value) . '"';
			}
			
			$records[] = '(' . join(',', $record) . ')';
		}
		
		// Execute insert query
		$result = $this->getDbConnection()->createCommand(
			'insert into ' . $this->tableName() . ' (`' . join('`,`', $fields) . '`) values ' . join(',', $records)
		)->execute();
		if ($result === false) {
			return false;
		}
		
		// Cleare all cache for current order
		$this->cleareCache($songs[0]['order_id']);
		
		return true;
	}
}