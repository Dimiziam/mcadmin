<?php
/**
 * SearchStaticPageForm
 * 
 * Class for search static page form
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SearchStaticPageForm extends StaticPage {

	/**
	 * Variable to store selected language
	 * 
	 * @var string
	 */
	public $language;
	
	/**
	 * Method return field rules
	 * 
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// Safe rule
			array('language', 'safe', 'on' => 'search'),
		);
	}
}
