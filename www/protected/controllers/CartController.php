<?php
/**
*
* @author Roman A. Shtempeliuk (deRamzes@gmail.com)
 * date  03.12.2013
 * @version  1.0
 */




class cartController extends Controller
{
	public function init() {
		parent::init();
		$this->layout='//layouts/main';
	}



	function actionIndex() {

		echo 123;

	}

/**
 * add songs to cart
 *
 */

	function actionAddToCart() {



		// parse data from js
		// and clean empty
//
		if (empty($_POST['track'])) {
			//TODO: translate text
			echo  json_encode(array('message'=>'track list empty'));
			Yii::app()->end();
		}

		if (empty($_POST['id_album'])) {
			//TODO: translate text
			echo  json_encode(array('message'=>'album id is wrong'));
			Yii::app()->end();
		}

		$track_to_buy=array_filter($_POST['track']);
		$id_album =  (int)$_POST['id_album'];




		if (Yii::app()->request->isAjaxRequest) {
			Yii::log('info','ajax request');


			// proced add to cart
			// store in redis, but id to session

			if (defined ( 'UserLogIn' )) {
				// add to user cart

				$user_id  =  $this->user_login;

			}

			else {

				//start session

				Yii::app()->session['user_not_register']=true;


				// time then start adding to card
				// use for cleanup, and group indent
				if (empty(Yii::app()->session['time_start'])) {
					Yii::app()->session['time_start']= time();
				}


				$time=	Yii::app()->session['time_start'];

				$user_id = Yii::app()->session->getSessionID();
			}

				// add items to cart
				// time added to card
				TRedis::instance( 'cart' )->setByTag( 'user_'.$user_id, $id_album , $track_to_buy );


				$a = TRedis::instance( 'cart' )->getByTag('user_'.$user_id);
						print_r($a);

				//TODO: translate text
				echo  json_encode(array('message'=>'user not logetin'));


		}
		else {
			Yii::log('info','normal request');

		}



		//echo json_encode($track_to_buy);//return 'ssss';

		//if user auth
		//else if user not  auth

	}



}