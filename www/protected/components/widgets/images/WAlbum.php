<?php
/**
 * WAlbum
 * 
 * Widget to prepare and display URL for album cover image
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class WAlbum extends CWidget
{
	/**
	 * Variable to store singer id
	 * 
	 * @var int
	 */
	public $singerId;
	
	/**
	 * Variable to store album id
	 * 
	 * @var int
	 */
	public $albumId;
	
	/**
	 * Variable to store album image type
	 * 
	 * @var int
	 */
	public $type;
	
	/**
	 * Method to run widget
	 * 
	 * @return void
	 */
	public function run()
	{
		echo Yii::app()->params['static_images_url'] 
			. Yii::app()->params['albumr_cover'] 
			. $this->type . $this->singerId . '_' . $this->albumId . '.' . ($this->type == 'cover' ? 'jpg' : 'gif');
	}
}
