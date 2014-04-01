<?php
/**
 * OrderController
 * 
 * Controller to process orders
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class OrderController extends Controller {
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('addToCart', 'cart', 'cartConfirm', 'list', 'songs', 'removeFromCart'),
				'users' => array('@')
			),
			array('allow',
				'actions' => array('addToCart', 'cart', 'removeFromCart'),
				'users' => array('?')
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * Action to display all songs for current order id
	 * 
	 * @param int $id
	 * @return void
	 */
	public function actionSongs($id)
	{
		// Get order songs
		$orderSongs = OrderSong::model()->getByOrderId($id, Yii::app()->user->id);
		if (empty($orderSongs)) {
			return $this->render('songs');
		}
		
		// Collect ids
		$albumIds = array();
		$songIds = array();
		foreach ($orderSongs as $song) {
			$albumIds[] = $song['album_id'];
			$songIds[] = $song['song_id'];
		}
		
		
		// Get info and render view
		$this->render('songs', 
			array(
				'orderSongs' => $orderSongs,
				'songs' => Song::model()->getByIds($songIds),
				'albums' => Album::model()->getByIds($albumIds, true)
			)
		);
	}
	
	/**
	 * Action to display list of current user orders
	 * 
	 * @return void
	 */
	public function actionList()
	{
		// Get info and render view
		$this->render('list', 
			array(
				'orders' => Order::model()->getByUserId(Yii::app()->user->id)
			)
		);
	}
	
	/**
	 * Action to confirm cart
	 * 
	 * @return void
	 */
	public function actionCartConfirm()
	{
		// Prepare default result
		$result = new stdClass;
		$result->status = 'error';
		
		// Get current cart info
		$info = MTCart::instance()->getInfo();
		if (empty($info) || empty($info['song_ids'])) {
			$result->message = MTLang::t('message_6', 'cart');
			$this->sendJson($result);
		}
		
		// Get songs
		$songs = Song::model()->getByIds($info['song_ids']);
		if (empty($songs)) {
			$result->message = MTLang::t('message_7', 'cart');
			$this->sendJson($result);
		}
		
		// Create new order
		$order = new Order('create');
		
		// Fill order
		$order->user_id = Yii::app()->user->id;
		$order->songs_number = sizeof($songs);
		$order->created = date('Y-m-d H:i:s', time());
		
		// Save new order
		if (!$order->save()) {
			$result->message = MTLang::t('message_8', 'cart');
			$this->sendJson($result);
		}
		
		// Prepare order songs
		$orderSongs = array();
		$totalPrice = 0;
		foreach ($songs as $song) {
			// Calculate price
			$location = 0;
			$price = round(Yii::app()->params['royalty'] * 100 + ($song['size'] / (1024 * 1024)) * Yii::app()->params['track_price'][$location]['price']) / 100;
			
			// Calculate total price
			$totalPrice += $price;
			
			// Add order song
			$orderSongs[] = array(
				'user_id' => Yii::app()->user->id,
				'order_id' => $order->id,
				'album_id' => $song['album_id'],
				'song_id' => $song['id'],
				'price' => $price,
				'created' => date('Y-m-d H:i:s', time())
			);
		}
		
		// Get current user info
		$userInfo = Yii::app()->user->getInfo();
		
		// Check is user has enought money
		if (empty($userInfo['amount']) || $userInfo['amount'] < $totalPrice) {
			// Delete order
			$order->delete();
			
			// Send error
			$result->message = MTLang::t('message_9', 'cart');
			$this->sendJson($result);
		}
		
		// Create transaction
		$transaction = new Transaction('create');
		
		// Fill transaction
		$transaction->user_id = Yii::app()->user->id;
		$transaction->order_id = $order->id;
		$transaction->type = 'sub';
		$transaction->status = 'done';
		$transaction->amount = $totalPrice;
		$transaction->date_start = date('Y-m-d H:i:s', time());
		$transaction->date_end = date('Y-m-d H:i:s', time());
		
		// Save transaction
		if (!$transaction->save()) {
			// Delete order
			$order->delete();
			
			// Send error
			$result->message = MTLang::t('message_8', 'cart');
			$this->sendJson($result);
		}
		
		// Fill order
		$order->price = $totalPrice;
		$order->transaction_id = $transaction->id;
		
		// Save order
		if (!$order->save()) {
			$result->message = MTLang::t('message_8', 'cart');
			$this->sendJson($result);
		}
		
		// Store order songs
		if (!OrderSong::model()->storeSongs($orderSongs)) {
			// Delete order
			$order->delete();
			
			// Delete transaction
			$transaction->delete();
			
			// Send error
			$result->message = MTLang::t('message_8', 'cart');
			$this->sendJson($result);
		}
		
		// Cleare user cache
		Yii::app()->user->cleareCache();
		
		// Cleare cart
		MTCart::instance()->cleare();
		
		// Send success result
		$result->status = 'success';
		$this->sendJson($result);
	}
	
	/**
	 * Action to display cart of current user
	 * 
	 * @return void
	 */
	public function actionCart()
	{
		// Get current cart info
		$info = MTCart::instance()->getInfo();
		if (empty($info) || empty($info['song_ids'])) {
			return $this->render('cart');
		}
		
		// Get info and render view
		$this->render('cart', 
			array(
				'songs' => Song::model()->getByIds($info['song_ids']),
				'albums' => Album::model()->getByIds($info['album_ids'], true)
			)
		);
	}
	
	/**
	 * Action to remove album or song from cart
	 * 
	 * @param string $type
	 * @param string $id
	 * @return void
	 */
	public function actionRemoveFromCart($type, $id)
	{
		// Prepare default result
		$result = new stdClass;
		$result->status = 'error';
		
		switch ($type) {
			case 'song':
				// Add songs to cart
				$addedNumber = MTCart::instance()->removeSong($id);
				if (empty($addedNumber)) {
					$this->sendJson($result);
				}
				
				// Fill result
				$result->status = 'success';
			break;
			
			case 'album':
				// Add albums to cart
				$addedSongsNumber = MTCart::instance()->removeAlbum($id);
				if (empty($addedSongsNumber)) {
					$this->sendJson($result);
				}
				
				// Fill result
				$result->status = 'success';
			break;
		}
		
		// Send result
		$this->sendJson($result);
	}
	
	/**
	 * Action to add albums or songs to cart
	 * 
	 * @param string $type
	 * @return void
	 */
	public function actionAddToCart($type)
	{
		// Prepare default result
		$result = new stdClass;
		$result->status = 'error';
		
		switch ($type) {
			case 'songs':
				// Check songs
				if (empty($_REQUEST['song_ids'])) {
					$this->sendJson($result);
				}
				
				// Add songs to cart
				$addedNumber = MTCart::instance()->addSongs($_REQUEST['song_ids']);
				if (empty($addedNumber)) {
					$this->sendJson($result);
				}
				
				// Fill result
				$result->status = 'success';
				$result->added_number = $addedNumber;
			break;
			
			case 'albums':
				// Check songs
				if (empty($_REQUEST['album_ids'])) {
					$this->sendJson($result);
				}
				
				// Add albums to cart
				$addedSongsNumber = MTCart::instance()->addAlbums($_REQUEST['album_ids']);
				if (empty($addedSongsNumber)) {
					$this->sendJson($result);
				}
				
				// Fill result
				$result->status = 'success';
				$result->added_number = $addedSongsNumber;
			break;
		}
		
		// Get current cart info
		$info = MTCart::instance()->getInfo();
		
		// Set total songs number to result
		$result->total_number = $info['songs_number'];
		
		// Send result
		$this->sendJson($result);
	}
}