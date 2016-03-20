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
	 * @var flag remove image or not
	 */
	public $remove_image;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 * @return Resource the static model class
	 */
	public static function model($className = __CLASS__)
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
			array('title, page_text', 'required', 'on' => 'create,update'),

			// Safe rules
			array('annotation,meta_description', 'safe', 'on' => 'create,update'),
			// image
			array('image', 'file', 'types' => 'jpg, gif, png', 'safe' => false, 'allowEmpty' => true),
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
			'meta_description' => MTLang::t('meta_description', 'static_page'),
			'remove_image' => MTLang::t('remove_image', 'static_page'),

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

		// get image file name to unlink
		$command_select = $this->getDbConnection()->createCommand(
			'select image from ' . $this->tableName() . '
			where page_id = :pageId'
		);
		$command_select->bindParam(':pageId', $pageId, PDO::PARAM_INT);
		$images = $command_select->queryAll();


		// Prepare delete command
		$command = $this->getDbConnection()->createCommand(
			'delete from ' . $this->tableName() . '
			where page_id = :pageId'
		);

		// Bind params
		$command->bindParam(':pageId', $pageId, PDO::PARAM_INT);

		// Delete
		if ($command->execute()>0) {
			//delete images
			foreach ($images as $item) {
				if (!empty($item['image'])) {
					unlink(Yii::app()->params['paths']['newsImages'] . $item['image']);
				}
			}
			return true;
		}

		return false;

	}

	/**
	 * Upload image filre
	 * @param
	 * @return  object CUploadedFile
	 */

	public function uploadNewsImage()
	{
		$uploadedFile = CUploadedFile::getInstance($this, 'image');
		if ($uploadedFile) {
			// rand
			$rnd = mt_rand(0, 9999);
			$uploadedFileName = "{$rnd}-{$uploadedFile}";  // random number + file name
			//clear
			$uploadedFileName = preg_replace('/[^a-zA-Z0-9.]/', '', $uploadedFileName);
			//set file name
			$this->image = $uploadedFileName;
			return $uploadedFile;
		}

	}

}