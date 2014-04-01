<?php
/**
 * WCountries
 * 
 * Widget to display countries dropdown list
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
class WCountries extends CWidget
{
	public function run()
	{
		$this->render('countries', array('countries' => Country::model()->getByLanguage(Yii::app()->params['currentLanguage'])));
	}
}
