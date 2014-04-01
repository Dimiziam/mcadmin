<?php
/**
 * AlbumStyle
 * 
 * Model for table album_styles
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class AlbumStyle extends InfoModel
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
		return 'album_styles';
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
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->deleteByTag('ALBUM_STYLES');
	}
	
	/**
	 * Method return all styles by album id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByAlbumId($id)
	{
		// Get style ids from cache
		$styleIds = TRedis::instance('data')->get('album_styles_' . $id);
		
		if (empty($styleIds)) {
			// Get style ids from DB
			$styleIds = $this->getDbConnection()
				->createCommand('select style_id from ' . $this->tableName() . ' where album_id = :id')
				->bindParam(":id", $id, PDO::PARAM_INT)
				->queryAll();
			if (empty($styleIds)) {
				return false;
			}
			
			// Prepare style ids
			$prepared = array();
			foreach ($styleIds as $styleId) {
				$prepared[] = $styleId['style_id'];
			}
			$styleIds = $prepared;
			
			// Store style ids in cache
			TRedis::instance('data')->setByTag('ALBUM_STYLES', 'album_styles_' . $id, $styleIds);
		}
		
		// Get styles by ids
		return Style::model()->getByIds($styleIds);
	}
}