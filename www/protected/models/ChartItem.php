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
			' . (!empty($params['date']) ? 'and created = :created' : '') . '
			order by position
			limit ' . ($this->getCurrentPage() * $this->getRowsPerPage()) . ', ' . $this->getRowsPerPage()
		);
		
		// Bind params
		$command->bindParam(":sourceId", $sourceId, PDO::PARAM_INT);
		if (!empty($params['id'])) {
			$command->bindParam(":id", $params['id'], PDO::PARAM_INT);
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
