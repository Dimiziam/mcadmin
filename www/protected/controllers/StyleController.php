<?php
/**
 * StyleController
 * 
 * Controller to work with styles
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class StyleController extends Controller	{

	/**
	 * Method to display main styles page
	 * 
	 * @param int $genreId
	 * @param string $alias
	 * @param int $id
	 * @return void
	 */
	public function actionIndex($genreId, $alias, $id) {
		$this->renderWithCache('<language>/styles/<genreId>/<alias>.<id>.html', 
			array(
				'<language>' => Yii::app()->params['currentLanguage'],
				'<genreId>' => $genreId,
				'<alias>' => $alias,
				'<id>' => $id,
			),
			'index',
			array(
				'currentGenreId' => $genreId,
				'currentStyleId' => $id,
				'genres' => Genre::model()->getAll(),
				'styles' => Style::model()->getByGenreId($genreId),
				'singers' => Singer::model()->getByStyleId($id)
			)
		);
	}
}