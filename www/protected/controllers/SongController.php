<?php
/**
 * SongController
 * 
 * Controller to work with songs
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class SongController extends Controller	{

	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('lyrics', 'lyricsAjax'),
				'users' => array('@')
			),
			array('allow',
				'actions' => array('lyrics', 'lyricsAjax'),
				'users' => array('?')
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}
	
	/**
	 * Action to display song text by ajax
	 * 
	 * @param string $singerLetter
	 * @param string $singerAlias
	 * @param string $albumAlias
	 * @param string $alias
	 * @param int $id
	 * @return void
	 */
	public function actionLyricsAjax($singerLetter, $singerAlias, $albumAlias, $alias, $id) 
	{
		$this->_lyrics($singerLetter, $singerAlias, $albumAlias, $alias, $id, true);
	}
	
	/**
	 * Action to display song text
	 * 
	 * @param string $singerLetter
	 * @param string $singerAlias
	 * @param string $albumAlias
	 * @param string $alias
	 * @param int $id
	 * @return void
	 */
	public function actionLyrics($singerLetter, $singerAlias, $albumAlias, $alias, $id) 
	{
		$this->_lyrics($singerLetter, $singerAlias, $albumAlias, $alias, $id);
	}
	
	/**
	 * Method to display song text
	 * 
	 * @param string $singerLetter
	 * @param string $singerAlias
	 * @param string $albumAlias
	 * @param string $alias
	 * @param int $id
	 * @param bool $ajax
	 * @return void
	 */
	private function _lyrics($singerLetter, $singerAlias, $albumAlias, $alias, $id, $ajax = false)
	{
		// Get song by id
		$song = Song::model()->getByIds(array($id));
		if (empty($song[$id]) || empty($song[$id]['text'])) {
			return $this->render('lyrics');
		}
		$song = $song[$id];

		// Prepare cache template
		$cacheTemplate = '<language>/lyrics' . (!empty($ajax) ? '_ajax' : '') . '/<singerLetter>/<singerAlias>/<albumAlias>/<alias>.<id>.html';
		
		// Prepare method name
		$method = empty($ajax) ? 'renderWithCache' : 'renderPartialWithCache';
		
		// Render view
		$this->{$method}($cacheTemplate, 
			array(
				'<language>' => Yii::app()->params['currentLanguage'],
				'<singerLetter>' => $singerLetter,
				'<singerAlias>' => $singerAlias,
				'<albumAlias>' => $albumAlias,
				'<alias>' => $alias,
				'<id>' => $id,
				'<ajax>' => 'ajax',
			),
			'lyrics',
			array(
				'song' => $song,
				'album' => Album::model()->getById($song['album_id']),
				'singer' => Singer::model()->getById($song['singer_id']),
				'text' => SongText::model()->getById($song['id']),
				'ajax' => $ajax
			)
		);
	}
}