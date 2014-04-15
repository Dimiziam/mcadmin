<?php
/**
 * SearchChartSourceForm
 * 
 * Class for search chart sources form
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SearchChartSourceForm extends ChartSource {

	/**
	 * Method return field rules
	 * 
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// Safe rule
			array('id, title', 'safe', 'on' => 'search'),
		);
	}
}
