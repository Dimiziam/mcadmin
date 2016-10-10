<?php
/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 11.10.2016
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */
 

class UserSubscrition extends InfoModel{

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
		return 'user_subscriptions';
	}

	/**
	 * Method return subscription info by user id
	 *
	 * @param int $userId
	 * @return mixed
	 * @throws CException
	 */
	public function getByUserId($userId)
	{
		// Prepare cache key
		$key = 'user_subscription_' . $userId;

		// Try to get subscription from cache
		//$subscription = TRedis::instance('users')->get($key);

		if (empty($subscription)) {
				// Get subscription from DB
				$subscription = $this->getDbConnection()
					->createCommand('select * from ' . $this->tableName() . ' where user_id=:userId')
					->bindParam(':userId', $userId, PDO::PARAM_INT)
					->queryRow();

			if (empty($subscription)) {
				return false;
			}

			// Store user into cache
			TRedis::instance('users')->setByTag('USER_SUBSCRIPTION' , $key, $subscription);
		}

		return $subscription;
	}


}