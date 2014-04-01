<?php
/**
 * WGenres
 * 
 * Widget to display genres dropdown list
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class WGenres extends CWidget
{
	public function run()
	{
		$this->render('genres', array('genres' => Genre::model()->getAll()));
	}
}
