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
	public static function model($className=__CLASS__)
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
			array('singer_id', 'numerical', 'integerOnly'=>true, 'on' => 'create'),

			// Formats rules
			array('title, keywords', 'length', 'max' => 255, 'on' => 'create,update'),

			//safe
			array('singer_id', 'safe', 'on' => 'search'),


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
			'singer_id' => MTLang::t('singer_id', 'seo'),
			'title' => MTLang::t('title', 'seo'),
			'description' => MTLang::t('description', 'seo'),
			'keywords' => MTLang::t('keywords', 'seo'),
			'text_seo' => MTLang::t('text_seo', 'seo'),

		);
	}

}