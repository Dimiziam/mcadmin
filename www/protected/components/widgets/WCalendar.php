<?php
/**
 * WCalendar
 * 
 * Widget to create calendar
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class WCalendar extends CWidget
{
	/**
	 * Variable to store current date
	 * 
	 * @var int
	 */
	public $currentDate;
	
	/**
	 * Variable to store settings
	 * 
	 * @var int
	 */
	public $settings = array();
	
	public function run()
	{
		$this->render('calendar', array('currentDate' => $this->currentDate, 'settings' => $this->settings));
	}
}
