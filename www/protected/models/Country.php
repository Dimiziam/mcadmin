<?php
/**
 * Country
 * 
 * Model for table countries
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Country extends InfoModel
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
		return 'countries';
	}
	
	/**
	 * Method return table name in old DB
	 * 
	 * @return string
	 */
	public function oldTableName()
	{
		return 'country';
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
			'country_ru' => 'country_ru',
			'country_en' => 'country_en',
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
				$item['alias'] = MTCommon::createAlias(trim($item['country_en']));
				
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
	 * Method return countries list by language
	 * 
	 * @param string $language
	 */
	public function getByLanguage($language)
	{
		// Get all countries
		$countries = $this->getAll();
		if (empty($countries)) {
			return false;
		}
		
		// Return countries for current language
		return !empty($countries[$language]) ? $countries[$language] : (!empty($countries[Yii::app()->language]) ? $countries[Yii::app()->language] : false);
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->delete('countries')
			&& MTCache::instance('models')->delete('countries');
	}
	
	/**
	 * Method reaturn all countries
	 * 
	 * @return array
	 */
	public function getAll()
	{
		// Check app cache
		if (!MTCache::instance('models')->test('countries')) {
			// Get list of countries from cache
			$countries = TRedis::instance('data')->get('countries');
			if (!empty($countries)) {
				
				// Store into app cache
				MTCache::instance('models')->set('countries', $countries);
				
				// Return countries
				return $countries;
			}
		} else {
			// Get countries from app cache
			return MTCache::instance('models')->get('countries');
		}
		
		// Get list of countries from DB
		$countries = $this->getDbConnection()
			->createCommand('select * from ' . $this->tableName() . ' order by alias')
			->queryAll();
		if (empty($countries)) {
			return false;
		}
		
		// Prepare list of countries
		$prepared = array();
		foreach ($countries as $country) {
			// Process fields
			foreach ($country as $key => $value) {
				if (!stristr($key, 'country_')) {
					continue;
				}
				
				// Prepare language
				$language = str_replace('country_', '', $key);
				
				// Store country
				$prepared[$language] = !empty($prepared[$language]) ? $prepared[$language] : array();
				$prepared[$language][$country['id']] = array(
					'id' => $country['id'], 
					'alias' => $country['alias'], 
					'name' => $value
				);
			}
		}
		
		// Store list of countries into Redis cache
		TRedis::instance('data')->set('countries', $prepared);
		
		// Store list of countries into app cache
		MTCache::instance('models')->set('countries', $prepared);
		
		return $prepared;
	}
}