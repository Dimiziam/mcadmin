<?php
/**
 * WLatestAlbums
 * 
 * Widget to display latest albums list
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class WLatestAlbums extends CWidget
{
	public function run()
	{
		$this->render('latest_albums', array('albums' => Album::model()->getLatestAlbums()));
	}
}
