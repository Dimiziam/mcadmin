<?php
/**
 * SongText
 * 
 * Model for table song_text
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SongText extends InfoModel
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
		return 'song_text';
	}
	
	/**
	 * Method return table name in old DB
	 * 
	 * @return string
	 */
	public function oldTableName()
	{
		return 'songtext';
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
	 * Method return relations between table field in old and new DB
	 * 
	 * @return array
	 */
	public function migrationFields()
	{
		return array(
			'id' => 'id',
			'text' => 'text'
		);
	}
	
	/**
	 * Method for migration hooks
	 * 
	 * @param string $type
	 * @param array $item
	 * @return array
	 */
	public function migrationHooks($type, $item = array())
	{
		switch ($type) {
			case 'item':
				return $item;
			break;
			
			case 'after':
				// Cleare all cache after migration was end
				$this->cleareCache();
			break;
		}
		
		return true;
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->deleteByTag('SONG_TEXTS');
	}
	
	/**
	 * Method return song text by id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id)
	{
		// Try to get song text from cache
		$text = TRedis::instance('data')->get('song_text_' . $id);
		
		if (empty($album)) {
			// Get song text from DB
			$text = $this->getDbConnection()
				->createCommand('select * from ' . $this->tableName() . ' where id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryRow();
			if (empty($text)) {
				return false;
			}
			
			// Store song text into cache
			TRedis::instance('data')->setByTag('SONG_TEXTS', 'song_text_' . $id, $text);
		}
		
		return $text;
	}
}