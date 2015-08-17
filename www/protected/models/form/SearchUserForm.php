<?php
/**
 * SearchUserForm
 * 
 * Class for search user form
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SearchUserForm extends User {


	public $show_download_all;


	/**
	 * Method return field rules
	 * 
	 * @return array validation rules for model attributes.
	 */

	public function rules()
	{
		return array(
			// Safe rule
			array('id, email, show_download_all', 'safe', 'on' => 'search'),
		);
	}



}
