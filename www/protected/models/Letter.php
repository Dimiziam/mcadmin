<?php
/**
 * Letter
 * 
 * Model for table letters
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Letter extends InfoModel
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
		return 'letters';
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
	 * Method return letter id by letter
	 * 
	 * @param string $letter
	 * @return mixed
	 */
	public function getIdByLetter($letter)
	{
		// Prepare letter
		$letter = strtoupper($letter);
		
		// Get all letters
		$letters = $this->getAll();
		
		// Return
		return !empty($letters[$letter]) ? $letters[$letter] : false;
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->delete('letters')
			&& MTCache::instance('models')->delete('letters');
	}
	
	/**
	 * Method reaturn all letters
	 * 
	 * @return array
	 */
	public function getAll()
	{
		// Check app cache
		if (!MTCache::instance('models')->test('letters')) {
			// Get list of letters from cache
			$letters = TRedis::instance('data')->get('letters');
			if (!empty($letters)) {
				// Store into app cache
				MTCache::instance('models')->set('letters', $letters);
				
				// Return letters
				return $letters;
			}
		} else {
			// Get letters from app cache
			return MTCache::instance('models')->get('letters');
		}
		
		// Get list of letters from DB
		$letters = $this->getDbConnection()
			->createCommand('select * from ' . $this->tableName())
			->queryAll();
		if (empty($letters)) {
			return false;
		}
		
		// Prepare list of letters
		$prepared = array();
		foreach ($letters as $letter) {
			$prepared[$letter['letter']] = $letter['id'];
		}
		
		// Store list of letters
		TRedis::instance('data')->set('letters', $prepared);
		
		// Store list of letters into app cache
		MTCache::instance('models')->set('letters', $prepared);
		
		return $prepared;
	}
}