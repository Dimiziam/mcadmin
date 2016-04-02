<?php
/**
 * Album
 * 
 * Model for table albums
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Album extends InfoModel
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
		return 'albums';
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
	 * method to search album name by part of string
	 * @param $q search strin
	 * @param $l limits
	 * @throws CDbException
	 * @return array albums from database
	 */

	public function search($q='',$l=20) {
		$albums = $this->getDbConnection()->createCommand("
			SELECT a.id as id,a.title,s.name sname from " . Album::model()->tableName() . " as a
			left join singers as s on s.id=a.singer_id
			where a.title LIKE :name
			LIMIT :limit
			");
		$q ='%'.$q.'%';
		$albums->bindParam(':name', $q , PDO::PARAM_STR);

		$albums->bindParam(':limit', $l , PDO::PARAM_INT);


		$albums = $albums->queryAll();
		return $albums;
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
		if ($result === true && !empty($this->id)) {
			// Remove all cache
			$this->cleareCache();
		}

		return $result;
	}


	/**
	 * Method to cleare current model cache
	 *
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->deleteByTag('ALBUM');
	}



}