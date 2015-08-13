<?php

/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 31.07.2015
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */
class SingerSeo extends InfoModel
{


	/**
	 * Method return DB table name
	 *
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'singer_seo';
	}


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 * @return Players the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
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
			array('singer_id', 'required', 'on' => 'create'),
			array('singer_id', 'numerical', 'integerOnly' => true, 'on' => 'create'),


			array('singer_id', 'uniqueSingerId', 'on' => 'create'),

			// Formats rules
			array('title, keywords', 'length', 'max' => 255, 'on' => 'create,update'),

			//safe
			array('description,text_seo,keywords', 'safe', 'on' => 'create,update'),
			//
			array('singer_id', 'safe', 'on' => 'search'),


		);
	}


	/**
	 * Method to check is singer_id is unique
	 *
	 * @param string $attribute
	 * @param array $params
	 */
	public function uniqueSingerId($attribute, $params)
	{
		// Try to find item by email
		$item = self::model()->find('singer_id = :singer_id', array(':singer_id' => $this->singer_id));

		// Check is item exists
		if (!empty($item) && (empty($this->id) || $item->id != $this->id)) {
			$this->addError('singer_id', MTLang::t('singer_exists', 'seo'));
		}
	}


	/**
	 * Method return attribute labels
	 *
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'singer_id' => MTLang::t('singer_id', 'seo'),
			'title' => MTLang::t('title', 'seo'),
			'description' => MTLang::t('description', 'seo'),
			'keywords' => MTLang::t('keywords', 'seo'),
			'text_seo' => MTLang::t('text_seo', 'seo'),

		);
	}


	public function search($params)
	{

		$command = $this->getDbConnection()->createCommand()
			->select("singerSeo.*, singer.name", 'SQL_CALC_FOUND_ROWS')
			->from($this->tableName() . ' as singerSeo')
			->leftJoin(Singer::model()->tableName() . ' singer', 'singer.id=singerSeo.singer_id')
			//->order('id desc')
			->limit($this->getRowsPerPage(), $this->getCurrentPage() * $this->getRowsPerPage());


		// Bind params
		if (!empty($params['singer_id'])) {
			pc::debug($params);
			$command->where('singerSeo.singer_id=:singer_id', array(":singer_id" => $params['singer_id']));
		}


		// Try to find users
		$singers = $command->queryAll();

		// Check users
		if (empty($singers)) {
			return false;
		}

		// Get nuber of found rows
		$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();

		// Get data provider
		$provider = new MTArrayDataProvider($singers,
			array(
				'totalItemCount' => $foundRows,
				'pagination' => array('pageSize' => $this->getRowsPerPage()),
			)
		);

		// Set offset
		$provider->setOffset(0);

		return $provider;


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
		return TRedis::instance('data')->deleteByTag('SINGERSEO');
	}


}