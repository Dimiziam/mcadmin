<?php
/**
 * WPager
 * 
 * Widget to create pager
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class WPager extends CWidget
{
	/**
	 * Variable to store data provider
	 * 
	 * @var MTArrayDataProvider
	 */
	public $provider;
	
	/**
	 * Variable to store settings
	 * 
	 * @var int
	 */
	public $settings = array();
	
	public function run()
	{
		$this->render('pager', array('provider' => $this->provider, 'settings' => $this->settings));
	}
}
