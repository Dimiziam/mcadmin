<?php
/**
 * InfoModel
 * 
 * Parent class for all info DB models
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class InfoModel extends CActiveRecord
{
	/**
	 * Variable to store result message
	 * 
	 * @var array
	 */
	private $_resultMessage = array();
	
	/**
	 * Method to migrate data from old DB to new
	 * 
	 * @param CDbConnection $oldDbConnection
	 * @param array $params
	 * @return mixed
	 */
	public function migration(CDbConnection $oldDbConnection, $params = array())
	{
		// Get old table name
		$oldTableName = $this->oldTableName();
		if (empty($oldTableName)) {
			$this->addResult('no_old_table_name', 'Wasn\'t set old table name');
			return false;
		}
		
		// Get relations between fields
		$fields = $this->migrationFields();
		if (empty($fields)) {
			$this->addResult('no_fields', 'Wasn\'t set migration rfields');
			return false;
		}
		
		// Call before migration hook
		if (!$this->migrationHooks('before')) {
			return false;
		}
		
		// Truncate table
		try {
			$this->getDbConnection()->createCommand()->truncateTable($this->tableName());
		} catch (Exception $e) {
			$this->addResult('not_truncated', $e->getCode() . '|' . $e->getMessage());
			return false;
		}
		
		// Get data from old DB
		$items = $oldDbConnection->createCommand(
			'select ' . (!empty($params['limit']) ? 'SQL_CALC_FOUND_ROWS' : '') . ' * 
			from ' . $oldTableName . '
			' . (!empty($params['limit']) ? 'limit 0, ' . $params['limit'] : '')
		)->queryAll();
		if (empty($items)) {
			$this->addResult('no_source_data', 'Not found data in old DB');
			return false;
		}
		
		// Calculate iterations number
		if (!empty($params['limit'])) {
			$foundResult = $oldDbConnection->createCommand('SELECT found_rows() as totalRows')->queryRow();
			if (empty($foundResult)|| empty($foundResult['totalRows'])) {
				$this->addResult('no_total_rows', 'Not found dtotal rows info');
				return false;
			}
			$iterations = ceil($foundResult['totalRows'] / $params['limit']);
		} else {
			$iterations = 1;
		}
		
		// Process iterations
		$addedNumber = 0;
		for ($i=0; $i<$iterations; $i++) {
			// Get nex items
			if ($i > 0) {
				$items = $oldDbConnection->createCommand(
					'select * 
					from ' . $oldTableName . '
					' . (!empty($params['limit']) ? 'limit ' . ($i * $params['limit']) . ', ' . $params['limit'] : '')
				)->queryAll();
				if (empty($items)) {
					$this->addResult('no_source_data', 'Not found data in old DB');
					return false;
				}
			}
			
			// Prepare insert data
			$records = array();
			foreach ($items as $item) {
				// Prepare record
				$record = array();
				
				// Call prepare item hook
				$item = $this->migrationHooks('item', $item);
				
				// Process fields
				foreach ($fields as $field => $newField) {
					// Add value
					if (!isset($item[$field])) {
						continue;
					}
					$record[] = '"' . addslashes($item[$field]) . '"';
				}
				
				// Check record
				if (empty($record)) {
					continue;
				}
				
				// Add record
				$records[] = '(' . join(',', $record) . ')';
				
				// Increase added numbers
				$addedNumber ++;
			}
			
			// Check records
			if (empty($records)) {
				$this->addResult('no_insert_data', 'Insert data wasn\'t prepared');
				return false;
			}
			
			// Create new command
			$command = $this->getDbConnection()->createCommand(
				'insert into ' . $this->tableName() . ' (`' . join('`,`', array_values($fields)) . '`) values ' . join(',', $records)
			);
			
			// Execute insert query
			$result = $command->execute();
			if ($result === false) {
				unset($command);
				unset($records);
				$this->addResult('add_error', 'Data wasn\'t added into new table');
				return false;
			}
			unset($records);
			unset($command);
			
			// Call after iteration hook
			if (!$this->migrationHooks('iteration_after')) {
				return false;
			}
		}
		
		// Call after migration hook
		if (!$this->migrationHooks('after')) {
			return false;
		}
		
		return $addedNumber;
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
		if ($type == 'item') {
			return $item;
		} else {
			return true;
		}
	}
	
	/**
	 * Method return relations between table field in old and new DB
	 * 
	 * @return array
	 */
	public function migrationFields()
	{
		return array();
	}
	
	/**
	 * Method return table name in old DB
	 * 
	 * @return string
	 */
	public function oldTableName()
	{
		return '';
	}
	
	/**
	 * Method to add result message
	 * 
	 * @param string $code
	 * @param string $message
	 * @return void
	 */
	public function addResult($code, $message)
	{
		$this->_resultMessage = array(
			'code' => $code,
			'message' => $message
		);
	}
	
	/**
	 * Method to get result message
	 * 
	 * @return array
	 */
	public function getResult()
	{
		return $this->_resultMessage;
	}
	
	/**
	 * Returns the database connection used by active record.
	 * By default, the "db" application component is used as the database connection.
	 * You may override this method if you want to use a different database connection.
	 * @return CDbConnection the database connection used by active record.
	 */
	public function getDbConnection()
	{
		// Check app cache
		if (MTCache::instance('connections')->test('info_db')) {
			// Get connection form app cache
			return MTCache::instance('connections')->get('info_db');
		}
		
		// Connect to DB
		$infoDb = new CDbConnection(
			Yii::app()->params['db']['info_db']['connectionString'],
			Yii::app()->params['db']['info_db']['username'],
			Yii::app()->params['db']['info_db']['password']
		);
		
		// Check connection
		if($infoDb instanceof CDbConnection) {
			// Set attributes
			if (!empty(Yii::app()->params['db']['info_db']['attributes'])) {
				// Process attributes
				foreach (Yii::app()->params['db']['info_db']['attributes'] as $key => $value) {
					$infoDb->{$key} = $value;
				}
			}
			
			// Store connection into app cache
			MTCache::instance('connections')->set('info_db', $infoDb);
			
			return $infoDb;
		} else {
			throw new CDbException(Yii::t('yii', 'Info DB model requires a "db" CDbConnection application component.'));
		}
	}
}
