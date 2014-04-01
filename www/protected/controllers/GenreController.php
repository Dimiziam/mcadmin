<?php
/**
 * GenreController
 * 
 * Controller to work with genres
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class GenreController extends Controller	{

	/**
	 * Method to display main genres page
	 * 
	 * @param string $alias
	 * @param int $id
	 * @return void
	 */
	public function actionIndex($alias = null, $id = 0) {
		// Get styles
		$styles = array();
		if (!empty($id)) {
			$styles = Style::model()->getByGenreId($id);
		}
		
		// Prepare cache data
		if (empty($alias) || empty($id)) {
			$cacheTemplate = '<language>/genres.html';
		} else {
			$cacheTemplate = '<language>/genres/<alias>.<id>.html';
		}
		
		$this->renderWithCache($cacheTemplate, 
			array(
				'<language>' => Yii::app()->params['currentLanguage'],
				'<alias>' => $alias,
				'<id>' => $id,
			),
			'index',
			array(
				'currentGenreId' => $id,
				'genres' => Genre::model()->getAll(),
				'styles' => $styles
			)
		);
	}
}