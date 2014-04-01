<?php
/**
 * WLetters
 * 
 * Widget to display search letters
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
class WLetters extends CWidget
{
	public function run()
	{
		$this->render('letters', array('letters' => Letter::model()->getAll()));
	}
}
