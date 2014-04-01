<?php
/**
 * Singer
 * 
 * Model for table singers
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Singer extends InfoModel
{
	/**
	 * Variable to store values for query to add relations between singers and styles
	 * 
	 * @var array
	 */
	private $_singersStylesValues = array();
	
	/**
	 * Variable to store letters array
	 * 
	 * @var array
	 */
	private $_letters = array();
	
	/**
	 * Variable to store letters values
	 * 
	 * @var array
	 */
	private $_letterValues = array();
	
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
		return 'singers';
	}
	
	/**
	 * Method return table name in old DB
	 * 
	 * @return string
	 */
	public function oldTableName()
	{
		return 'singer';
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
			'prepared_alias' => 'alias',
			'name' => 'name',
			'descr' => 'description',
			'page' => 'page',
			'country' => 'country_id',
			'dir' => 'dir',
			'alias' => 'pseudonym',
			'permit_from' => 'permit_from',
			'composer' => 'is_composer'
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
				$item['name'] = trim($item['name']);
				
				// Prepare alias
				$item['prepared_alias'] = MTCommon::createAlias($item['name']);
				if (empty($item['prepared_alias'])) {
					$item['prepared_alias'] = MTCommon::createAlias($item['alias']);
				}
				
				// Prepare composer
				$item['composer'] = $item['composer'] == 1 ? 'yes' : 'no';
				
				// Prepare singer styles values
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
							$this->_singersStylesValues[] = '(' . $item['id'] . ', ' . $styleId . ')';
						}
					}
				}
				
				// Add letter id for name
				$firstLetter = strtoupper(substr($item['name'], 0, 1));
				if (!empty($this->_letters[$firstLetter])) {
					$this->_letterValues[] = '(' . $item['id'] . ', ' . $this->_letters[$firstLetter] . ', "name")';
				} else {
					$this->_letterValues[] = '(' . $item['id'] . ', ' . $this->_letters['#'] . ', "name")';
				}
				
				// Add letter id for pseudonym
				$firstLetter = strtoupper(substr($item['alias'], 0, 1));
				if (!empty($this->_letters[$firstLetter])) {
					$this->_letterValues[] = '(' . $item['id'] . ', ' . $this->_letters[$firstLetter] . ', "pseudonym")';
				} else {
					$this->_letterValues[] = '(' . $item['id'] . ', ' . $this->_letters['#'] . ', "pseudonym")';
				}
				
				return $item;
			break;
			
			case 'iteration_after':
				if (!empty($this->_singersStylesValues)) {
					// Insert relations between singers and styles
					$result = $this->getDbConnection()->createCommand(
						'insert ignore into ' . SingerStyle::model()->tableName() . ' (`singer_id`, `style_id`) 
						values ' . join(',', $this->_singersStylesValues)
					)->execute();
					if ($result === false) {
						$this->addResult('singer_styles_error', 'Wasn\'t added relations between singers and styles');
						return false;
					}
					
					// Cleare relations values
					$this->_singersStylesValues = array();
				}
				
				if (!empty($this->_letterValues)) {
					// Insert relations between singers and styles
					$result = $this->getDbConnection()->createCommand(
						'insert ignore into ' . SingerLetter::model()->tableName() . ' (`singer_id`, `letter_id`, `type`) 
						values ' . join(',', $this->_letterValues)
					)->execute();
					if ($result === false) {
						$this->addResult('singer_letters_error', 'Wasn\'t added relations between singers and letters');
						return false;
					}
					
					// Cleare leters values
					$this->_letterValues = array();
				}
			break;
			
			case 'before':
				// Load letters
				$this->_letters = Letter::model()->getAll();
				if (empty($this->_letters)) {
					$this->addResult('not_found_letters', 'Wasn\'t found letters');
					return false;
				}
				
				// Truncate singer letters table
				try {
					$this->getDbConnection()->createCommand()->truncateTable(SingerLetter::model()->tableName());
				} catch (Exception $e) {
					$this->addResult('singer_letters_not_truncated', $e->getCode() . '|' . $e->getMessage());
					return false;
				}
				
				// Truncate singer styles table
				try {
					$this->getDbConnection()->createCommand()->truncateTable(SingerStyle::model()->tableName());
				} catch (Exception $e) {
					$this->addResult('singer_styles_not_truncated', $e->getCode() . '|' . $e->getMessage());
					return false;
				}
				
				// Truncate singer genres table
				try {
					$this->getDbConnection()->createCommand()->truncateTable(SingerGenre::model()->tableName());
				} catch (Exception $e) {
					$this->addResult('singer_genres_not_truncated', $e->getCode() . '|' . $e->getMessage());
					return false;
				}
			break;
			
			case 'after':
				// Add relations between singers and genres
				if (SingerGenre::model()->addRelations() === false) {
					$this->addResult('singer_genres_add_error', 'Wasn\'t added relations between singers and genres');
					return false;
				}
				
				// Cleare all cache for singers
				$this->cleareCache();
				
				// Cleare all cache for relations between singers and genres
				SingerGenre::model()->cleareCache();
				
				// Cleare all cache for relations between singers and styles
				SingerStyle::model()->cleareCache();
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
		return TRedis::instance('data')->deleteByTag('SINGERS');
	}
	
	/**
	 * Method to search singers by letter
	 * 
	 * @param string $letter
	 * @return CSqlDataProvider
	 */
	public function searchByLetter($letter)
	{
		// Get letter id
		$letterId = Letter::model()->getIdByLetter($letter);
		if (empty($letterId)) {
			return false;
		}
		
		// Get rpp
		$rpp = !empty(Yii::app()->params['pager']['rpp']['singer_letters']) ? Yii::app()->params['pager']['rpp']['singer_letters'] : 20;
		
		// Get current page
		$page = !empty($_GET[Yii::app()->params['pager']['variable']]) ? (int) $_GET[Yii::app()->params['pager']['variable']] - 1 : 0;
		
		// Prepare cache keys
		$key = 'singets_by_letter_' . $letterId . '_' . $rpp . '_' . $page;
		$foundRowsKey = 'singets_by_letter_found_rows_' . $letterId;
		
		// Try to get singers from cache
		$singers = TRedis::instance('data')->get($key);
		
		// Try to get found rowas from cache
		$foundRows = TRedis::instance('data')->get($foundRowsKey);
		
		// Get singers form DB
		if (empty($singers)) {
			$singers = $this->getDbConnection()->createCommand(
				'select ' . (empty($foundRows) ? 'SQL_CALC_FOUND_ROWS' : '') . ' singers.* 
				from ' . $this->tableName() . ' singers
					inner join ' . SingerLetter::model()->tableName() . ' singer_letters 
						on singers.id = singer_letters.singer_id 
							and singer_letters.letter_id = ' . $letterId . '
				group by singers.id
				order by singers.name
				limit ' . ($page * $rpp) . ', ' . $rpp
			)->queryAll();
			
			// Check singers
			if (empty($singers)) {
				return false;
			}
			
			// Store singers into cache
			TRedis::instance('data')->setByTag('SINGERS', $key, $singers);
			
			// Get numbers of found rows from DB
			if (empty($foundRows)) {
				$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
				
				// Store numbers of found rows into cache
				TRedis::instance('data')->setByTag('SINGERS', $foundRowsKey, $foundRows);
			}
		}
		
		// Get data provider
		$provider = new MTArrayDataProvider($singers, 
			array(
				'totalItemCount' => $foundRows,
				'pagination' => array('pageSize' => Yii::app()->params['pager']['rpp']['singer_letters']),
			)
		);
		
		// Set offset
		$provider->setOffset(0);
		
		return $provider;
	}

	/**
	 * Method return singers by style id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByStyleId($id)
	{
		// Try to get singers from cache
		$singers = TRedis::instance('data')->get('style_singers_' . $id);
		
		if (empty($singers)) {
			// Get singers from DB
			$singers = $this->getDbConnection()
				->createCommand(
					'select singers.* 
					from ' . $this->tableName() . ' singers
						inner join ' . SingerStyle::model()->tableName() . ' ss 
							on ss.singer_id = singers.id
								and ss.style_id = :id
					order by singers.name'
				)
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryAll();
			if (empty($singers)) {
				return false;
			}
			
			// Store singers into cache
			TRedis::instance('data')->setByTag('SINGERS', 'style_singers_' . $id, $singers);
		}
		
		return $singers;
	}
	
	/**
	 * Method return singers by country id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByCountryId($id)
	{
		// Try to get singers from cache
		$singers = TRedis::instance('data')->get('country_singers_' . $id);
		
		if (empty($singers)) {
			// Get singers from DB
			$singers = $this->getDbConnection()
				->createCommand(
					'select * 
					from ' . $this->tableName() . ' 
					where country_id = :id
					order by name'
				)
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryAll();
			if (empty($singers)) {
				return false;
			}
			
			// Store singers into cache
			TRedis::instance('data')->setByTag('SINGERS', 'country_singers_' . $id, $singers);
		}
		
		return $singers;
	}
	
	/**
	 * Method return singers list by ids
	 * 
	 * @param array $ids
	 * @return mixed
	 */
	public function getByIds($ids)
	{
		$result = array();
		
		// Try to get info from redis
		foreach ($ids as $id) {
			$info = TRedis::instance('data')->get('singer_' . $id);
			if (!empty($info)) {
				$result[$id] = $info;
			}
		}
		
		// Get not found ids
		$ids = array_diff($ids, array_keys($result));
		if (empty($ids)) {
			return $result;
		}
		
		// Get singers from DB
		$singers = $this->getDbConnection()
			->createCommand(
				'select *
				from ' . $this->tableName() . '
				where id in (' . join(',', array_map('intval', $ids)) . ')'
			)
			->queryAll();
		if (empty($singers)) {
			return array();
		}
		
		// Process singers
		foreach ($singers as $singer) {
			// Store info into cache
			TRedis::instance('data')->setByTag('SINGERS', 'singer_' . $singer['id'], $singer);
			
			// Set to result
			$result[$singer['id']] = $singer;
		}
		
		// Return info
		return $result;
	}
	
	/**
	 * Method return singer info by id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id)
	{
		// Try to get singer from cache
		$singer = TRedis::instance('data')->get('singer_' . $id);
		
		if (empty($singer)) {
			// Get singer from DB
			$singer = $this->getDbConnection()
				->createCommand('select * from ' . $this->tableName() . ' where id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryRow();
			if (empty($singer)) {
				return false;
			}
			
			// Store singer into cache
			TRedis::instance('data')->setByTag('SINGERS', 'singer_' . $id, $singer);
		}
		
		// Get countries for current language
		$countries = Country::model()->getByLanguage(Yii::app()->params['currentLanguage']);
		if (!empty($countries) && !empty($countries[$singer['country_id']])) {
			// Add country name
			$singer['country'] = $countries[$singer['country_id']];
		}
		
		// Add info about genres
		$singer['genres'] = SingerGenre::model()->getBySingerId($id);
		
		// Add info about styles
		$singer['styles'] = SingerStyle::model()->getBySingerId($id);
		
		return $singer;
	}
}