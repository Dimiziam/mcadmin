<?php
/**
 * OrderSong
 * 
 * Model for table order_songs
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class OrderSong extends InfoModel
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
		return 'order_songs';
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
	 * Method return list of all songs by order id
	 * 
	 * @param int $id
	 * @return mixed
	 */
	public function getByOrderId($id)
	{
		// Prepare DB command
		$command = $this->getDbConnection()
			->createCommand(
				'select order_songs.*, albums.title, songs.name
				from ' . $this->tableName() . ' order_songs
					inner join ' . Album::model()->tableName() . ' albums on albums.id = order_songs.album_id
					inner join ' . Song::model()->tableName() . ' songs on songs.id = order_songs.song_id
				where order_songs.order_id = :orderId
				order by order_songs.album_id'
			);
		
		// Bind params
		$command->bindParam(':orderId', $id, PDO::PARAM_INT);
			
		// Get list of songs from DB
		$songs = $command->queryAll();
		if (empty($songs)) {
			return false;
		}
		
		return $songs;
	}
}