<?php
/**
 * StaticPageLanguage
 * 
 * Model for table static_page_languages
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class StaticPageLanguage extends InfoModel
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
		return 'static_page_languages';
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
			array('title, annotation, page_text', 'required', 'on' => 'create,update'),
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
			'title' => MTLang::t('title', 'static_page'),
			'annotation' => MTLang::t('annotation', 'static_page'),
			'page_text' => MTLang::t('page_text', 'static_page'),
		);
	}
	
	/**
	 * Method to delete all language info by page id
	 * 
	 * @param int $pageId
	 * @return bool
	 */
	public function deleteByPageId($pageId)
	{
		// Prepare command
		$command = $this->getDbConnection()->createCommand(
			'delete from ' . $this->tableName() . '
			where page_id = :pageId'
		);
		
		// Bind params
		$command->bindParam(':pageId', $pageId, PDO::PARAM_INT);
		
		// Delete
		if ($command->execute() === false) {
			return false;
		}
		
		return true;
	}
}