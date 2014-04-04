<?php
/**
 * SearchOrderForm
 * 
 * Class for search order form
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SearchOrderForm extends Order {

	/**
	 * Variable to store user email
	 * 
	 * @param int
	 */
	public $email = null;
	
	/**
	 * Method return field rules
	 * 
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// Safe rule
			array('id, user_id', 'safe', 'on' => 'search'),
		);
	}
}
