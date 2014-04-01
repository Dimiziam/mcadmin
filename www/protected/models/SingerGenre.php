<?php
/**
 * SingerGenre
 * 
 * Model for table singer_genres
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SingerGenre extends InfoModel
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
		return 'singer_genres';
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
	 * Method to add relations between singers and genres
	 * 
	 * @return bool
	 */
	public function addRelations()
	{
		// Insert relations between singers and genres
		return $this->getDbConnection()->createCommand(
			'insert ignore into singer_genres (`singer_id`, `genre_id`)
			select singer_styles.singer_id, styles.genre_id
			from singer_styles
				inner join styles on styles.id = singer_styles.style_id'
		)->execute();
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->deleteByTag('SINGER_GENRES');
	}
	
	/**
	 * Method return all genres by singer id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getBySingerId($id)
	{
		// Get genre ids from cache
		$genreIds = TRedis::instance('data')->get('singer_genres_' . $id);
		
		if (empty($genreIds)) {
			// Get genre ids from DB
			$genreIds = $this->getDbConnection()
				->createCommand('select genre_id from ' . $this->tableName() . ' where singer_id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryAll();
			if (empty($genreIds)) {
				return false;
			}
			
			// Prepare genre ids
			$prepared = array();
			foreach ($genreIds as $genreId) {
				$prepared[] = $genreId['genre_id'];
			}
			$genreIds = $prepared;
			
			// Store genre ids in cache
			TRedis::instance('data')->setByTag('SINGER_GENRES', 'singer_genres_' . $id, $genreIds);
		}
		
		// Get genres by ids
		return Genre::model()->getByIds($genreIds);
	}
}