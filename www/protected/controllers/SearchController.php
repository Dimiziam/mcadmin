<?php
/**
 * SearchController
 * 
 * Controller with search actions
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
class SearchController extends Controller
{
	public function init() {
		parent::init();
		$this->layout='//layouts/main';
	}


	function actionIndex($id_singer,$search_auto) {


		// i

		$id_singer=intval($id_singer);
		//search by id
		if ($id_singer>0 ) {
			$singer_info = MediaClub::instance()->getSingerbyId($id_singer);
		}
		//search by part of string
		elseif (strlen($search_auto)>0 and $id_singer==0) {

		}



		//get AlbumsList
		if (empty($singer_info['error'])) {

			$albums_info = MediaClub::instance()->getAlbumsBySinger($singer_info['id']);

			$AlbumsDataProvider=new CArrayDataProvider($albums_info, array(

					'sort'=>array(
							'attributes'=>array(
									'year', 'dateup', 'datein',
							),
					),
					'pagination'=>array(
							'pageSize'=>Yii::app()->params['AlbumsPerPage'],
					),


			));
			// $dataProvider->getData() will return a list of arrays.
		}


		$this->render('/singer/singerView',array(
				'singer_info'=>$singer_info,
				'albums_info'=>$AlbumsDataProvider


		));



	}



	/**
	 * search ajax autocomplite
	 * @param integer $genre_search genre id
	 * @param string $search_auto search string
	 */
	function actionAjaxSearch($genre_search=0,$search_auto='') {

		if (strlen($search_auto)<=1)
		{
			Yii::app()->end();
		}
//		$a =  MediaClub::ajax_autocomliteSinger('7 da',20);

		foreach (Yii::app()->log->routes  as $route) {

			if ($route instanceof  CWebLogRoute) {
				$route->enabled=false;
			}
		}

	//echo	TRedis::instance('autocomplite')->AddToSortedSet(':ingers',strtolower('ramzes'),2);


		$search_string=$search_auto;
		 $genre = (int)$genre_search;

		echo  $res = MediaClub::instance()->ajax_autocompliteSphinx($search_string,$genre);
		Yii::app()->end();


	}


	/**
	 * Method to search singers by letters
	 * 
	 * @return void
	 */
	public function actionLetters($letter) {
		$this->render('letters',
			array(
				'letter' => $letter,
				'model' => Singer::model()->searchByLetter($letter)
			)
		);
	}



}
