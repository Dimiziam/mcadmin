<?php
/**
 * AlbumController
 * 
 * Controller to work with albums
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class AlbumController extends Controller	{

	/**
	 * Method to display malbums history
	 * 
	 * @param string $date
	 * @param int $genreId
	 * @return void
	 */
	public function actionHistory($date, $genreId = 0) {

		$this->render('history',
			array(
				'currentDate' => $date,
				'curentGenreId' => $genreId,
				'albums' => Album::model()->history($date, $genreId),
				'calendarInfo' => NewAlbumStatistic::model()->getForCalendar($date)
			)
		);
	}
	
	/**
	 * Method to display main album page
	 * 
	 * @param string $id
	 * @return void
	 */
	public function actionIndex($id) {
		// Get album
		$album = Album::model()->getById($id);
		if (empty($album)) {
			return $this->render('index');
		}
		
		$this->render('index',
			array(
				'singer' => Singer::model()->getById($album['singer_id']),
				'album' => $album,
				'songs' => Song::model()->getByAlbumId($id)
			)
		);
	}
}