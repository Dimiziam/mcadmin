<?php
/**
 * WPanel
 * 
 * Widget to display search panel
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
class WPanel extends CWidget
{
	public function run()
	{
		$this->render('panel', array('genres' => Genre::model()->getAll()));
	}
}
