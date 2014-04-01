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
	 * Variable to store values for query to add relations between albums and styles
	 * 
	 * @var array
	 */
	private $_albumsStylesValues = array();
	
	
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
	 * Method return table name in old DB
	 * 
	 * @return string
	 */
	public function oldTableName()
	{
		return 'album';
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
			'title' => 'title',
			'year' => 'year',
			'id_singer' => 'singer_id',
			'uploader' => 'uploader',
			'owner' => 'owner_id',
			'dir' => 'dir',
			'descr' => 'description',
			'alb_type' => 'type',
			'datein' => 'created',
			'dateup' => 'updated',
			'id_singer_tribute' => 'id_singer_tribute',
			'permit_from' => 'permit_from',
			'composer' => 'composer',
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
				$item['alias'] = MTCommon::createAlias(trim($item['title']));
				
				// Prepare album styles values
				if (!empty($item['id_style'])) {
					// Get style ids
					$styleIds = explode(';', trim($item['id_style'], ';'));
					if (!empty($styleIds)) {
						// Process style ids
						foreach ($styleIds as $styleId) {
							if (empty($styleId)) {
								continue;
							}
							
							// Add value
							$this->_albumsStylesValues[] = '(' . $item['id'] . ', ' . $styleId . ')';
						}
					}
				}
				
				return $item;
			break;
			
			case 'iteration_after':
				if (!empty($this->_albumsStylesValues)) {
					// Insert relations between albums and styles
					$result = $this->getDbConnection()->createCommand(
						'insert ignore into ' . AlbumStyle::model()->tableName() . ' (`album_id`, `style_id`) 
						values ' . join(',', $this->_albumsStylesValues)
					)->execute();
					if ($result === false) {
						$this->addResult('album_styles_error', 'Wasn\'t added relations between albums and styles');
						return false;
					}
					
					// Cleare relations values
					$this->_albumsStylesValues = array();
				}
			break;
			
			case 'before':
				// Truncate album styles table
				try {
					$this->getDbConnection()->createCommand()->truncateTable(AlbumStyle::model()->tableName());
				} catch (Exception $e) {
					$this->addResult('album_styles_not_truncated', $e->getCode() . '|' . $e->getMessage());
					return false;
				}
				
				// Truncate album genres table
				try {
					$this->getDbConnection()->createCommand()->truncateTable(AlbumGenre::model()->tableName());
				} catch (Exception $e) {
					$this->addResult('album_genres_not_truncated', $e->getCode() . '|' . $e->getMessage());
					return false;
				}
			break;
			
			case 'after':
				// Add relations between albums and genres
				if (AlbumGenre::model()->addRelations() === false) {
					$this->addResult('album_genres_add_error', 'Wasn\'t added relations between albums and genres');
					return false;
				}
				
				// Recalculate new albums statistics
				if (NewAlbumStatistic::model()->recalculate() === false) {
					$this->addResult('recalculate_albums_statistics_error', 'Wasn\'t recalculate new albums statistics');
					return false;
				}
				
				// Cleare all cache for albums
				$this->cleareCache();
				
				// Cleare all cache for albums genres
				AlbumGenre::model()->cleareCache();
				
				// Cleare all cache for albums styles
				AlbumStyle::model()->cleareCache();
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
		return TRedis::instance('data')->deleteByTag('ALBUMS');
	}
	
	/**
	 * Method return albums list by ids
	 * 
	 * @param array $ids
	 * @param bool $addSingersInfo
	 * @return mixed
	 */
	public function getByIds($ids, $addSingersInfo = false)
	{
		$result = array();
		$singerIds = array();
		
		// Try to get info from redis
		foreach ($ids as $id) {
			$info = TRedis::instance('data')->get('album_' . $id);
			if (!empty($info)) {
				$result[$id] = $info;
				
				// Collect singer ids
				$singerIds[] = $info['singer_id'];
			}
		}
		
		// Get not found ids
		$ids = array_diff($ids, array_keys($result));
		if (!empty($ids)) {
			// Get albums from DB
			$albums = $this->getDbConnection()
				->createCommand(
					'select *
					from ' . $this->tableName() . '
					where id in (' . join(',', array_map('intval', $ids)) . ')'
				)
				->queryAll();
			if (empty($albums)) {
				return array();
			}
			
			// Process albums
			foreach ($albums as $album) {
				// Store info into cache
				TRedis::instance('data')->setByTag('ALBUMS', 'album_' . $album['id'], $album);
				
				// Set to result
				$result[$album['id']] = $album;
				
				// Collect singer ids
				$singerIds[] = $album['singer_id'];
			}
		}
		
		// Add singers info
		if ($addSingersInfo && !empty($singerIds)) {
			// Get singers by ids
			$singers = Singer::model()->getByIds($singerIds);
			if (empty($singers)) {
				return $result;
			}
			
			// Process albums
			foreach ($result as $index => $album) {
				if (empty($singers[$album['singer_id']])) {
					continue;
				}
				
				// Set singer
				$result[$index]['singer'] = $singers[$album['singer_id']];
			}
		}
		
		// Return info
		return $result;
	}
	
	/**
	 * Method return album info by id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id)
	{
		// Try to get album from cache
		$album = TRedis::instance('data')->get('album_' . $id);
		
		if (empty($album)) {
			// Get album from DB
			$album = $this->getDbConnection()
				->createCommand('select * from ' . $this->tableName() . ' where id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryRow();
			if (empty($album)) {
				return false;
			}
			
			// Store album into cache
			TRedis::instance('data')->setByTag('ALBUMS', 'album_' . $id, $album);
		}
		
		// Add info about genres
		$album['genres'] = AlbumGenre::model()->getByAlbumId($id);
		
		// Add info about styles
		$album['styles'] = AlbumStyle::model()->getByAlbumId($id);
		
		return $album;
	}
	
	/**
	 * Method return info about latest albums
	 * 
	 * @return array
	 */
	public function getLatestAlbums()
	{
		// Get numbers
		$numbers = !empty(Yii::app()->params['lists']['albums']['index_latest_number']) ? Yii::app()->params['lists']['albums']['index_latest_number'] : 5;
		
		// Try to get albums from cache
		$albums = TRedis::instance('data')->get('latest_albums_' . $numbers);
		
		if (empty($albums)) {
			// Get albums from DB
			$albums = $this->getDbConnection()
				->createCommand(
					'select * 
					from ' . $this->tableName() . '
					order by created desc
					limit ' . (int) $numbers
				)->queryAll();
			if (empty($albums)) {
				return false;
			}
			
			// Store album into cache
			TRedis::instance('data')->setByTag('ALBUMS', 'latest_albums_' . $numbers, $albums);
		}
		
		// Add info
		return $this->_addInfoToAlbums($albums);
	}
	
	/**
	 * Method to add info to albums
	 * 
	 * @param array $albums
	 * @return array
	 */
	private function _addInfoToAlbums($albums)
	{
		if (empty($albums)) {
			return array();
		}
		
		// Collect ids
		$ids = array(
			'singer_ids' => array(),
			'album_ids' => array()
		);
		foreach ($albums as $album) {
			$ids['album_ids'][] = $album['id'];
			$ids['singer_ids'][] = $album['singer_id'];
		}
		
		// Add singers info
		if (!empty($ids['singer_ids'])) {
			// Get singers by ids
			$singers = Singer::model()->getByIds($ids['singer_ids']);
			if (!empty($singers)) {
				// Process albums
				foreach ($albums as $index => $album) {
					if (empty($singers[$album['singer_id']])) {
						continue;
					}
					
					// Set singer
					$albums[$index]['singer'] = $singers[$album['singer_id']];
				}
			}
		}
		
		// Add info about genres
		if (!empty($ids['album_ids'])) {
			// Get genres
			$genres = AlbumGenre::model()->getByAlbumIds($ids['album_ids']);
			if (!empty($genres)) {
				// Process albums
				foreach ($albums as $index => $album) {
					if (empty($genres[$album['id']])) {
						continue;
					}
					
					// Set singer
					$albums[$index]['genres'] = $genres[$album['id']];
				}
			}
		}
		
		return $albums;
	}
	
	/**
	 * Method to search albums by date and ganre id
	 * 
	 * @param string $date
	 * @param int $genreId
	 * @return mixed
	 */
	public function history($date, $genreId = 0)
	{
		// Get rpp
		$rpp = !empty(Yii::app()->params['pager']['rpp']['albums']) ? Yii::app()->params['pager']['rpp']['albums'] : 20;
		
		// Get current page
		$page = !empty($_GET[Yii::app()->params['pager']['variable']]) ? (int) $_GET[Yii::app()->params['pager']['variable']] - 1 : 0;
		
		// Prepare cache keys
		$key = 'history_albums_' . $date . '_' . $genreId . '_' . $rpp . '_' . $page;
		$foundRowsKey = 'history_albums_found_rows_' . $date . '_' . $genreId;
		
		// Try to get albums from cache
		$albums = TRedis::instance('data')->get($key);
		
		// Try to get found rowas from cache
		$foundRows = TRedis::instance('data')->get($foundRowsKey);
		
		if (empty($albums)) {
			// Create query command
			$command = $this->getDbConnection()->createCommand(
				'select ' . (empty($foundRows) ? 'SQL_CALC_FOUND_ROWS' : '') . ' albums.* 
				from ' . $this->tableName() . ' as albums
					' . (!empty($genreId) ? 'inner join ' . AlbumGenre::model()->tableName() . ' ag on albums.id = ag.album_id and ag.genre_id = :genreId' : '') . '
				where albums.created = :date
				' . (!empty($genreId) ? 'group by albums.id' : '') . '
				limit ' . ($page * $rpp) . ', ' . $rpp
			);
			
			// Bind params
			$command->bindParam(":date", $date, PDO::PARAM_STR);
			if (!empty($genreId)) {
				$command->bindParam(":genreId", $genreId, PDO::PARAM_INT);
			}
			
			// Try to find albums in DB
			$albums = $command->queryAll();
			
			// Check albums
			if (empty($albums)) {
				return false;
			}
			
			// Store singer into cache
			TRedis::instance('data')->setByTag('ALBUMS', $key, $albums);
			
			// Get numbers of found rows from DB
			if (empty($foundRows)) {
				$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
				
				// Store numbers of found rows into cache
				TRedis::instance('data')->setByTag('ALBUMS', $foundRowsKey, $foundRows);
			}
		}
		
		// Get data provider
		$provider = new MTArrayDataProvider($this->_addInfoToAlbums($albums), 
			array(
				'totalItemCount' => $foundRows,
				'pagination' => array('pageSize' => Yii::app()->params['pager']['rpp']['albums']),
			)
		);
		
		// Set offset
		$provider->setOffset(0);
		
		return $provider;
	}
	
	/**
	 * Method to search albums by singer id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function searchBySingerId($id)
	{
		// Get rpp
		$rpp = !empty(Yii::app()->params['pager']['rpp']['albums']) ? Yii::app()->params['pager']['rpp']['albums'] : 20;
		
		// Get current page
		$page = !empty($_GET[Yii::app()->params['pager']['variable']]) ? (int) $_GET[Yii::app()->params['pager']['variable']] - 1 : 0;
		
		// Prepare cache keys
		$key = 'singer_albums_' . $id . '_' . $rpp . '_' . $page;
		$foundRowsKey = 'singer_albums_found_rows_' . $id;
		
		// Try to get albums from cache
		$albums = TRedis::instance('data')->get($key);
		
		// Try to get found rowas from cache
		$foundRows = TRedis::instance('data')->get($foundRowsKey);
		
		if (empty($albums)) {
			// Try to find albums in DB
			$albums = $this->getDbConnection()->createCommand(
				'select ' . (empty($foundRows) ? 'SQL_CALC_FOUND_ROWS' : '') . ' * 
				from ' . $this->tableName() . '
				where singer_id = :id
				order by year desc
				limit ' . ($page * $rpp) . ', ' . $rpp
			)->bindParam(":id", $id, PDO::PARAM_INT)->queryAll();
			
			// Check albums
			if (empty($albums)) {
				return false;
			}
			
			// Store singer into cache
			TRedis::instance('data')->setByTag('ALBUMS', $key, $albums);
			
			// Get numbers of found rows from DB
			if (empty($foundRows)) {
				$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
				
				// Store numbers of found rows into cache
				TRedis::instance('data')->setByTag('ALBUMS', $foundRowsKey, $foundRows);
			}
		}
		
		// Get data provider
		$provider = new MTArrayDataProvider($albums, 
			array(
				'totalItemCount' => $foundRows,
				'pagination' => array('pageSize' => Yii::app()->params['pager']['rpp']['albums']),
			)
		);
		
		// Set offset
		$provider->setOffset(0);
		
		return $provider;
	}
	
	/**
	 * Method to update albums info
	 * 
	 * @return bool
	 */
	public function updateAlbums()
	{
		// Update albums info
		return $this->getDbConnection()->createCommand(
			'update albums set 
				size = (select sum(size) from songs where songs.album_id = albums.id),
				length = (select sum(length) from songs where songs.album_id = albums.id),
				bitrate = (select max(bitrate) from songs where songs.album_id = albums.id limit 1),
				songs_number = (select count(id) from songs where songs.album_id = albums.id)'
		)->execute();
	}
}