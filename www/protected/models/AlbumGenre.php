<?php
/**
 * AlbumGenre
 * 
 * Model for table album_genres
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class AlbumGenre extends InfoModel
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
		return 'album_genres';
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
	 * Method to add relations between albums and genres
	 * 
	 * @return bool
	 */
	public function addRelations()
	{
		// Insert relations between albums and genres
		return $this->getDbConnection()->createCommand(
			'insert ignore into album_genres (`album_id`, `genre_id`)
			select album_styles.album_id, styles.genre_id
			from album_styles
				inner join styles on styles.id = album_styles.style_id'
		)->execute();
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->deleteByTag('ALBUM_GENRES');
	}
	
	/**
	 * Method return all genres by album ids
	 * 
	 * @param array $ids
	 * @return mixed
	 */
	public function getByAlbumIds($ids)
	{
		$genreIds = array();
		$plaintGenreIds = array();
		
		// Try to get genre ids from redis
		foreach ($ids as $id) {
			$info = TRedis::instance('data')->get('album_genres_' . $id);
			if (!empty($info)) {
				$genreIds[$id] = $info;
				$plaintGenreIds = array_merge($plaintGenreIds, $info);
			}
		}
		
		// Get not found ids
		$ids = array_diff($ids, array_keys($genreIds));
		if (!empty($ids)) {
			// Get genre ids from DB
			$newGenreIds = $this->getDbConnection()
				->createCommand(
					'select album_id, genre_id 
					from ' . $this->tableName() . '
					where album_id in (' . join(',', array_map('intval', $ids)) . ')'
				)->queryAll();
			if (empty($newGenreIds)) {
				return false;
			}
			
			// Process genre ids
			$prepared = array();
			foreach ($newGenreIds as $genreId) {
				$prepared[$genreId['album_id']] = !empty($prepared[$genreId['album_id']]) ? $prepared[$genreId['album_id']] : array();
				$prepared[$genreId['album_id']][] = $genreId['genre_id'];
				
				$genreIds[$genreId['album_id']] = !empty($genreIds[$genreId['album_id']]) ? $genreIds[$genreId['album_id']] : array();
				$genreIds[$genreId['album_id']][] = $genreId['genre_id'];
				
				$plaintGenreIds[] = $genreId['genre_id'];
			}
			
			// Store genre ids in cache
			foreach ($prepared as $albumId => $ids) {
				TRedis::instance('data')->setByTag('ALBUM_GENRES', 'album_genres_' . $albumId, $ids);
			}
		}
		
		if (empty($plaintGenreIds)) {
			return false;
		}
		
		// Get genres info
		$genres = Genre::model()->getByIds(array_unique($plaintGenreIds));
		if (empty($genres)) {
			return false;
		}
		
		// Separate genres by albums
		$prepared = array();
		foreach ($genres as $id => $genre) {
			// Process ids
			foreach ($genreIds as $albumId => $ids) {
				if (!in_array($id, $ids)) {
					continue;
				}
				
				$prepared[$albumId] = !empty($prepared[$albumId]) ? $prepared[$albumId] : array();
				$prepared[$albumId][] = $genre;
			}
		}
		
		return $prepared;
	}
	
	/**
	 * Method return all genres by album id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByAlbumId($id)
	{
		// Get genre ids from cache
		$genreIds = TRedis::instance('data')->get('album_genres_' . $id);
		
		if (empty($genreIds)) {
			// Get genre ids from DB
			$genreIds = $this->getDbConnection()
				->createCommand('select genre_id from ' . $this->tableName() . ' where album_id = :id')
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
			TRedis::instance('data')->setByTag('ALBUM_GENRES', 'album_genres_' . $id, $genreIds);
		}
		
		// Get genres by ids
		return Genre::model()->getByIds($genreIds);
	}
}