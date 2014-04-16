<?php
/**
 * SearchChartItemForm
 * 
 * Class for search chart items form
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SearchChartItemForm extends ChartSource {

	/**
	 * Variable to store selected year
	 * 
	 * @var int
	 */
	public $year;
	
	/**
	 * Variable to store selected month
	 * 
	 * @var string
	 */
	public $month;
	
	/**
	 * Method return field rules
	 * 
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// Safe rule
			array('year, month', 'safe', 'on' => 'search'),
		);
	}
}
