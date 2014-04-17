<?php
/**
 * ChartItem
 * 
 * Model for table chart_items
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class ChartItem extends InfoModel {
	
	/**
	 * Returns the static model of the specified AR class.
	 * 
	 * @param string $className active record class name.
	 * @return Players the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Method return DB table name
	 * 
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'chart_items';
	}

	/**
	 * Method return field rules
	 * 
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// Required rules
			array('singer_id, album_id, position', 'required', 'on' => 'create,update'),
			
			// Safe rule
			array('singer, album', 'safe', 'on' => 'create,update'),
		);
	}

	/**
	 * Method return attribute labels
	 * 
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'singer_id' => MTLang::t('singer', 'chart'),
			'album_id' => MTLang::t('album', 'chart'),
			'position' => MTLang::t('position', 'chart'),
		);
	}
	
	/**
	 * After delete hook
	 * 
	 * @return void
	 */
	public function afterDelete()
	{
		// Remove all cache
		$this->cleareCache();
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
		return TRedis::instance('data')->deleteByTag('CHARTS');
	}
	
	/**
	 * Method to add new items
	 * 
	 * @param int $sourceId
	 * @param string $created
	 * @param array $items
	 * @return bool
	 */
	public function bulckAdd($sourceId, $created, $items)
	{
		// Prepare values
		$values = array();
		foreach ($items as $item) {
			$values[] = '(
				' . (int) $sourceId . ', 
				"' . addslashes($created) . '",
				' . (int) $item['position'] . ',
				"' . addslashes($item['singer']) . '",
				"' . addslashes($item['album']) . '"
			)';
		}
		
		// Prepare command
		$command = $this->getDbConnection()->createCommand(
			'insert into ' . $this->tableName() . ' (source_id, created, position, singer, album) 
			values ' . join(',', $values)
		);
		
		// Add
		if ($command->execute() === false) {
			return false;
		}
		
		// Cleare cache
		$this->cleareCache();
		
		return true;
	}
	
	/**
	 * Method to delete all items by source id
	 * 
	 * @param int $sourceId
	 * @param string $created
	 * @return bool
	 */
	public function deleteBySourceId($sourceId, $created = null)
	{
		// Prepare command
		$command = $this->getDbConnection()->createCommand(
			'delete from ' . $this->tableName() . '
			where source_id = :sourceId
				' . (!empty($created) ? ' and created = :created' : '')
		);
		
		// Bind params
		$command->bindParam(':sourceId', $sourceId, PDO::PARAM_INT);
		if (!empty($created)) {
			$command->bindParam(':created', $created, PDO::PARAM_STR);
		}
		
		// Delete
		if ($command->execute() === false) {
			return false;
		}
		
		// Cleare cache
		$this->cleareCache();
		
		return true;
	}
	
	/**
	 * Method to search chart items
	 * 
	 * @param int $sourceId
	 * @param array $params
	 * @return mixed
	 */
	public function search($sourceId, $params)
	{
		// Create query command
		$command = $this->getDbConnection()->createCommand(
			'select SQL_CALC_FOUND_ROWS * 
			from ' . $this->tableName() . '
			where source_id = :sourceId
			' . (!empty($params['created']) ? 'and created = :created' : '') . '
			order by position
			limit ' . ($this->getCurrentPage() * $this->getRowsPerPage()) . ', ' . $this->getRowsPerPage()
		);
		
		// Bind params
		$command->bindParam(":sourceId", $sourceId, PDO::PARAM_INT);
		if (!empty($params['created'])) {
			$command->bindParam(":created", $params['created'], PDO::PARAM_STR);
		}
		
		// Try to find chart items
		$items = $command->queryAll();
		
		// Check items
		if (empty($items)) {
			return false;
		}
		
		// Get nuber of found rows
		$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
		
		// Get data provider
		$provider = new MTArrayDataProvider($items, 
			array(
				'totalItemCount' => $foundRows,
				'pagination' => array('pageSize' => $this->getRowsPerPage()),
			)
		);
		
		// Set offset
		$provider->setOffset(0);
		
		return $provider;
	}
}
