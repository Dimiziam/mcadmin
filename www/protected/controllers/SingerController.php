<?php
/**
 * SingerController
 * 
 * Controller with search actions
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
class SingerController extends Controller	{

	public function init() {

		parent::init();

		//$this->layout='//layouts/main';

	}

	/**
	 * Method to display main singer page
	 * 
	 * @param string $id
	 * @return void
	 */
	public function actionIndex($id) {
		$this->render('index',
			array(
				'singer' => Singer::model()->getById($id),
				'albums' => Album::model()->searchBySingerId($id)
			)
		);
	}

	function actionAlbum($id,$album_name){

//		echo $id;
//		echo $album_name;

		$detail_track = MediaClub::instance()->getTracksByAlbumID($id);



		Yii::trace(CVarDumper::dumpAsString($detail_track),'vardump');

		$this->render('albumView',array(
				'singer_info'=>$detail_track['singer_info'],
				'album_info'=>$detail_track['album_info'],
				'tracks'=>$detail_track['tracks'],



		));

	}


}