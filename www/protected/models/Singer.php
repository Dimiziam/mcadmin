<?php
/**
 * Singer
 * 
 * Model for table singers
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Singer extends InfoModel
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
		return 'singers';
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
	 * Method return singer info by id
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id)
	{
		// Try to get singer from cache
		$singer = TRedis::instance('data')->get('singer_' . $id);

		if (empty($singer)) {
			// Get singer from DB
			$singer = $this->getDbConnection()
				->createCommand('select * from ' . $this->tableName() . ' where id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryRow();
			if (empty($singer)) {
				return false;
			}
			// Store singer into cache
			TRedis::instance('data')->setByTag('SINGERS', 'singer_' . $id, $singer);
		}

		// add singer info to runtime cache
		if (!MTCache::instance('singer')->test($id)) {
			MTCache::instance('singer')->set($id,$singer);
		}


		return $singer;
	}

}