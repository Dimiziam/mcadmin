<?php
/**
 * ChartSource
 * 
 * Model for table chart_sources
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class ChartSource extends InfoModel {
	
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
		return 'chart_sources';
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
			array('alias, title, url', 'required', 'on' => 'create,update'),
			
			// Formats rules
			array('alias, title, url', 'length', 'max' => 255, 'on' => 'create,update'),
			
			// Custom rules
			array('alias', 'uniqueAlias', 'on' => 'create,update'),
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
			'alias' => MTLang::t('alias', 'chart'),
			'title' => MTLang::t('title', 'chart'),
			'url' => MTLang::t('url', 'chart'),
		);
	}
	
	/**
	 * Method to check is alias unique
	 * 
	 * @param string $attribute
	 * @param array $params
	 */
	public function uniqueAlias($attribute, $params)
	{
		// Try to find item by login
		$item = self::model()->find('alias = :alias', array(':alias' => $this->alias));
		
		// Check is item exists
		if (!empty($item) && (empty($this->id) || $item->id != $this->id)) {
			$this->addError('login', MTLang::t('alias_exists', 'chart'));
		}
	}
	
	/**
	 * After delete hook
	 * 
	 * @return void
	 */
	public function afterDelete()
	{
		// Delete all itesm
		ChartItem::model()->deleteBySourceId($this->id);
		
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

		if ($result === true) {
			// Remove all cache after each update
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
		return TRedis::instance('charts')->deleteByTag('CHARTS');
	}
	
	/**
	 * Method to search chart sources
	 * 
	 * @param array $params
	 * @return mixed
	 */
	public function search($params)
	{
		// Create query command
		$command = $this->getDbConnection()->createCommand(
			'select SQL_CALC_FOUND_ROWS * 
			from ' . $this->tableName() . '
			' . (!empty($params['id']) ? 'where id = :id' : '') . '
			order by id desc
			limit ' . ($this->getCurrentPage() * $this->getRowsPerPage()) . ', ' . $this->getRowsPerPage()
		);
		
		// Bind params
		if (!empty($params['id'])) {
			$command->bindParam(":id", $params['id'], PDO::PARAM_INT);
		}
		
		// Try to find chart sources
		$sources = $command->queryAll();
		
		// Check sources
		if (empty($sources)) {
			return false;
		}
		
		// Get nuber of found rows
		$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
		
		// Get data provider
		$provider = new MTArrayDataProvider($sources, 
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
