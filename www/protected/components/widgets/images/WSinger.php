<?php
/**
 * WSinger
 * 
 * Widget to prepare and display URL for singer cover image
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class WSinger extends CWidget
{
	/**
	 * Variable to store singer id
	 * 
	 * @var int
	 */
	public $singerId;
	
	/**
	 * Method to run widget
	 * 
	 * @return void
	 */
	public function run()
	{
		echo Yii::app()->params['static_images_url'] 
			. Yii::app()->params['singer_cover'] 
			. 'artist_' . $this->singerId . '.jpg';
	}
}
