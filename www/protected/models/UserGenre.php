<?php
/**
 *
 * @package admin.mediaclub.com.
 * Date: 10.10.2016
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */
 

class UserGenre extends InfoModel {
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


	public function tableName()
	{
		return 'user_genres';
	}

}