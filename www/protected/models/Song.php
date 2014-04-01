<?php
/**
 * Song
 * 
 * Model for table songs
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Song extends InfoModel
{
	/**
	 * Variable to store relations between old and new modes
	 * 
	 * @var array
	 */
	private $_modeRelations = array(
		'Joint stereo' => 'stereo',
		'Dual channel' => 'stereo',
		'Joint Srereo' => 'stereo',
		'Mono' => 'mono',
		'Single channel' => 'mono',
		'Stereo' => 'stereo',
	);
	
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
		return 'songs';
	}
	
	/**
	 * Method return table name in old DB
	 * 
	 * @return string
	 */
	public function oldTableName()
	{
		return 'file';
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
			'id_album' => 'album_id',
			'path_to_file' => 'path',
			'alias' => 'alias',
			'name' => 'name',
			'year' => 'year',
			'size' => 'size',
			'time' => 'length',
			'mode' => 'mode',
			'comment' => 'comment',
			'bitrate' => 'bitrate',
			'track' => 'track',
			'text' => 'text',
			'md5' => 'md5',
			'owner' => 'owner_id',
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
				
				// Prepare mode
				$item['mode'] = trim($item['mode']);
				$item['mode'] = !empty($item['mode']) && !empty($this->_modeRelations[$item['mode']]) ? $this->_modeRelations[$item['mode']] : 'undefined';
				
				return $item;
			break;
			
			case 'after':
				// Update singer id
				$result = $this->getDbConnection()->createCommand(
					'update songs, albums set songs.singer_id = albums.singer_id where songs.album_id = albums.id'
				)->execute();
				if ($result === false) {
					$this->addResult('update_singer_id_error', 'Wasn\'t update singer ids');
					return false;
				}
				
				// Update albums info
				if (Album::model()->updateAlbums() === false) {
					$this->addResult('update_albums_error', 'Wasn\'t update albums info');
					return false;
				}
				
				// Cleare all cache for songs
				$this->cleareCache();
			break;
		}
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->deleteByTag('SONGS');
	}
	
	/**
	 * Method return songs list by ids
	 * 
	 * @param array $ids
	 * @return mixed
	 */
	public function getByIds($ids)
	{
		$result = array();
		
		// Try to get info from redis
		foreach ($ids as $id) {
			$info = TRedis::instance('data')->get('song_' . $id);
			if (!empty($info)) {
				$result[$id] = $info;
			}
		}
		
		// Get not found ids
		$ids = array_diff($ids, array_keys($result));
		if (empty($ids)) {
			return $result;
		}
		
		// Get songs from DB
		$songs = $this->getDbConnection()
			->createCommand(
				'select *
				from ' . $this->tableName() . '
				where id in (' . join(',', array_map('intval', $ids)) . ')'
			)
			->queryAll();
		if (empty($songs)) {
			return array();
		}
		
		// Process songs
		foreach ($songs as $song) {
			// Store info into cache
			TRedis::instance('data')->setByTag('SONGS', 'song_' . $song['id'], $song);
			
			// Set to result
			$result[$song['id']] = $song;
		}
		
		// Return info
		return $result;
	}
	
	/**
	 * Method return song ids by album ids
	 * 
	 * @param array $albumIds
	 * @return mixed
	 */
	public function getIdsByAlbumIds($albumIds)
	{
		if (empty($albumIds)) {
			return false;
		}
		
		// Prepare cache key
		$key = 'song_ids_by_album_ids_' . md5(join('', $albumIds));
		
		// Try to get ids from cache
		$ids = TRedis::instance('data')->get($key);
		
		if (empty($ids)) {
			// Get ids from DB
			$ids = $this->getDbConnection()
				->createCommand(
					'select id from ' . $this->tableName() . ' 
					where album_id in (' . join(',', array_map('intval', $albumIds)) . ')'
				)->queryColumn();
			if (empty($ids)) {
				return false;
			}

			// Store ids into cache
			TRedis::instance('data')->setByTag('SONGS', $key, $ids);
		}
		
		return $ids;
	}
	
	/**
	 * Method return songs list by album id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByAlbumId($id)
	{
		// Try to get songs from cache
		$songs = TRedis::instance('data')->get('album_songs_' . $id);
		
		if (empty($songs)) {
			// Get songs from DB
			$songs = $this->getDbConnection()
				->createCommand('select * from ' . $this->tableName() . ' where album_id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryAll();
			if (empty($songs)) {
				return false;
			}
			
			// Prepare songs
			$prepared = array();
			foreach ($songs as $song) {
				$prepared[$song['id']] = $song;
			}
			$songs = $prepared;
			
			// Store songs into cache
			TRedis::instance('data')->setByTag('SONGS', 'album_songs_' . $id, $songs);
		}
		
		return $songs;
	}
}