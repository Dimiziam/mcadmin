<?php
/**
 * StaticPage
 * 
 * Model for table static_pages
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class StaticPage extends InfoModel
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
		return 'static_pages';
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
	 * Method return field rules
	 * 
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// Required rules
			array('alias, type', 'required', 'on' => 'create'),
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
			'alias' => MTLang::t('alias', 'static_page'),
			'type' => MTLang::t('type', 'static_page')
		);
	}
	
	/**
	 * After delete hook
	 * 
	 * @return void
	 */
	public function afterDelete()
	{
		// Delete language info
		StaticPageLanguage::model()->deleteByPageId($this->id);
		
		// Remove all cache
		$this->cleareCache($this->type);
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
			$this->cleareCache($this->type);
		}
		
		return $result;
	}
	
	/**
	 * Method to cleare current model cache for some type
	 * 
	 * @param int $id
	 * @return bool
	 */
	public function cleareCache($type)
	{
		return TRedis::instance('data')->deleteByTag('STATIC_PAGES_' . $type);
	}
	
	/**
	 * Method to search static pages
	 * 
	 * @param array $params
	 * @return mixed
	 */
	public function search($params)
	{
		// Create query command
		$command = $this->getDbConnection()->createCommand(
			'select SQL_CALC_FOUND_ROWS page.*,
				def.title as default_title, def.annotation as default_annotation, def.page_text as default_page_text,
				cur.title as current_title, cur.annotation as current_annotation, cur.page_text as current_page_text 
			from ' . $this->tableName() . ' page
				inner join ' . StaticPageLanguage::model()->tableName() . ' def on def.page_id = page.id and `def`.`language` = "' . addslashes(Yii::app()->language) . '"
				left join ' . StaticPageLanguage::model()->tableName() . ' cur on cur.page_id = page.id and `cur`.`language` = "' . addslashes($params['language']) . '"
			where 1 
				' . (!empty($params['type']) ? ' and page.type = :type' : '') . '
			order by page.id desc
			limit ' . ($this->getCurrentPage() * $this->getRowsPerPage()) . ', ' . $this->getRowsPerPage()
		);
		
		// Bind params
		if (!empty($params['type'])) {
			$command->bindParam(":type", $params['type'], PDO::PARAM_STR);
		}
		
		// Try to find pages
		$pages = $command->queryAll();
		
		// Check pages
		if (empty($pages)) {
			return false;
		}
		
		// Get nuber of found rows
		$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();
		
		// Get data provider
		$provider = new MTArrayDataProvider($pages, 
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