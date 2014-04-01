<?php
/**
 * Style
 * 
 * Model for table styles
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Style extends InfoModel
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
		return 'styles';
	}
	
	/**
	 * Method return table name in old DB
	 * 
	 * @return string
	 */
	public function oldTableName()
	{
		return 'style';
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
			'id_geenre' => 'genre_id',
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
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->deleteByTag('STYLES')
			&& MTCache::instance('styles')->cleareCache();
	}
	
	/**
	 * Method return style by id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id)
	{
		// Load all styles info into redis cache
		if (!$this->_loadToRedis()) {
			return false;
		}
		
		// Prepare cache key
		$key = 'style_' . $id;
		
		// Check app cache
		if (!MTCache::instance('styles')->test($key)) {
			$style = TRedis::instance('data')->get($key);
			
			// Store style into app cache
			MTCache::instance('styles')->set($key, $style);
		} else {
			// Get style from app cache
			$style = MTCache::instance('styles')->get($key);
		}
		
		return $style;
	}
	
	/**
	 * Method return styles by ids
	 * 
	 * @param array $ids
	 * @return mixed
	 */
	public function getByIds($ids)
	{
		// Load all styles info into redis cache
		if (!$this->_loadToRedis()) {
			return false;
		}
		
		// Prepare cache key
		$key = 'styles_by_ids_' . md5(join('_', $ids));
		
		// Check app cache
		if (!MTCache::instance('styles')->test($key)) {
			// Prepare style keys
			$keys = array();
			foreach ($ids as $id) {
				$keys[] = 'style_' . $id;
			}
			
			// Get styles
			$styles = TRedis::instance('data')->get($keys);
			if (empty($styles)) {
				return false;
			}
			
			// Prepare styles
			$prepared = array();
			foreach ($styles as $style) {
				$prepared[$style['id']] = $style;
			}
			
			// Store list of styles into app cache
			MTCache::instance('styles')->set($key, $prepared);
		} else {
			// Get styles from app cache
			$prepared = MTCache::instance('styles')->get($key);
		}
		
		return $prepared;
	}
	
	/**
	 * Method return styles by genre id
	 * 
	 * @param int $genreId
	 * @return mixed
	 */
	public function getByGenreId($genreId)
	{
		// Load all styles info into redis cache
		if (!$this->_loadToRedis()) {
			return false;
		}
		
		// Prepare cache key
		$key = 'genre_styles_' . $genreId;
		
		// Check app cache
		if (!MTCache::instance('styles')->test($key)) {
			// Get styles info
			$styles = TRedis::instance('data')->get($key);
			
			// Store list of styles into app cache
			MTCache::instance('styles')->set($key, $styles);
		} else {
			// Get styles from app cache
			$styles = MTCache::instance('styles')->get($key);
		}
		
		// Get styles
		return !empty($styles) ? $styles : false;
	}
	
	/**
	 * Method to load all styles to redis cache
	 * 
	 * @return bool
	 */
	protected function _loadToRedis()
	{
		// Check is styles already in cache
		if (TRedis::instance('data')->get('is_styles_in_cache')) {
			return true;
		}
		
		// Clear all styles cache
		$this->cleareCache();
		
		// Get list of styles from DB
		$styles = $this->getDbConnection()
			->createCommand('select * from ' . $this->tableName() . ' order by name')
			->queryAll();
		if (empty($styles)) {
			return false;
		}
		
		// Process styles
		$relations = array();
		foreach ($styles as $style) {
			// Prepare relation between genre and styles
			$relations[$style['genre_id']] = !empty($relations[$style['genre_id']]) ? $relations[$style['genre_id']] : array();
			$relations[$style['genre_id']][] = $style;
			
			// Store style info
			if (!TRedis::instance('data')->setByTag('STYLES', 'style_' . $style['id'], $style)) {
				return false;
			}
		}
		
		// Process relations
		foreach ($relations as $genreId => $relation) {
			// Store relation between genre and styles
			TRedis::instance('data')->setByTag('STYLES', 'genre_styles_' . $genreId, $relation);
		}
		
		// Store cached flag
		return TRedis::instance('data')->setByTag('STYLES', 'is_styles_in_cache', true);
	}
}