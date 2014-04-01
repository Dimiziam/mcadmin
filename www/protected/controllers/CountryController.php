<?php
/**
 * CountryController
 * 
 * Controller to work with countries
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class CountryController extends Controller	{

	/**
	 * Method to display main countries page
	 * 
	 * @param string $alias
	 * @param int $id
	 * @return void
	 */
	public function actionIndex($alias = null, $id = 0) {
		// Get singers
		$singers = array();
		if (!empty($id)) {
			$singers = Singer::model()->getByCountryId($id);
		}
		
		// Prepare cache data
		if (empty($alias) || empty($id)) {
			$cacheTemplate = '<language>/countries.html';
		} else {
			$cacheTemplate = '<language>/countries/<alias>.<id>.html';
		}

		$this->renderWithCache($cacheTemplate, 
			array(
				'<language>' => Yii::app()->params['currentLanguage'],
				'<alias>' => $alias,
				'<id>' => $id,
			),
			'index',
			array(
				'currentCountryId' => $id,
				'countries' => Country::model()->getByLanguage(Yii::app()->params['currentLanguage']),
				'singers' => $singers
			)
		);
	}
}