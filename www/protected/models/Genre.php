<?php
/**
 * Genre
 * 
 * Model for table genres
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Genre extends InfoModel
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
		return 'genres';
	}
	
	/**
	 * Method return table name in old DB
	 * 
	 * @return string
	 */
	public function oldTableName()
	{
		return 'geenre';
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
			'alias' => 'alias',
			'name' => 'name',
			'descr' => 'description',
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
				// Prepare alias
				$item['alias'] = MTCommon::createAlias(trim($item['name']));
				
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
	 * Method return genre names by ids
	 * 
	 * @param array $ids
	 * @return array
	 */
	public function getByIds($ids)
	{
		// Get all genres
		$genres = $this->getAll();
		
		// Process ids
		$prepared = array();
		foreach ($ids as $id) {
			if (empty($genres[$id])) {
				continue;
			}
			
			$prepared[$id] = $genres[$id];
		}
		
		// Return
		return $prepared;
	}
	
	/**
	 * Method return genre name by id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id)
	{
		// Get all genres
		$genres = $this->getAll();
		
		// Return
		return !empty($genres[$id]) ? $genres[$id] : false;
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->delete('genres')
			&& MTCache::instance('models')->delete('genres');
	}
	
	/**
	 * Method reaturn all genres
	 * 
	 * @return array
	 */
	public function getAll()
	{
		// Check app cache
		if (!MTCache::instance('models')->test('genres')) {
			// Get list of genres from cache
			$genres = TRedis::instance('data')->get('genres');
			if (!empty($genres)) {
				// Store into app cache
				MTCache::instance('models')->set('genres', $genres);
				
				// Return genres
				return $genres;
			}
		} else {
			// Get genres from app cache
			return MTCache::instance('models')->get('genres');
		}
		
		// Get list of genres from DB
		$genres = $this->getDbConnection()
			->createCommand('select * from ' . $this->tableName() . ' order by name')
			->queryAll();
		if (empty($genres)) {
			return false;
		}
		
		// Prepare list of genres
		$prepared = array();
		foreach ($genres as $genre) {
			$prepared[$genre['id']] = $genre;
		}
		
		// Store list of genres
		TRedis::instance('data')->set('genres', $prepared);
		
		// Store list of genres into app cache
		MTCache::instance('models')->set('genres', $prepared);
		
		return $prepared;
	}
}