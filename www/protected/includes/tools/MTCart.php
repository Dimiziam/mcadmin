<?php
/**
 * MTCart
 * 
 * Class to work with cart
 * 
 * @package MediaClub
 * @subpackage tools
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MTCart
{
	/**
	 * Variable to store instance of class
	 * 
	 * @var MTCart
	 */
	public static $instance = null;
	
	/**
	 * Variable to store user token
	 * 
	 * @var string
	 */
	public $token = null;
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		// Prepare cache key
		if (Yii::app()->user->isGuest) {
			$key = 'cart_by_token_' . $this->token;
		} else {
			$key = 'cart_by_user_' . Yii::app()->user->id;
		}
		
		return TRedis::instance('cart')->delete($key)
			&& MTCache::instance('cart')->delete($key);
	}
	
	/**
	 * Method to remove song by id
	 * 
	 * @param int $songId
	 * @return mixed
	 */
	public function removeSong($songId)
	{
		if (empty($songId)) {
			return false;
		}
		
		// Get current cart info
		$info = $this->getInfo();
		if (empty($info)) {
			return false;
		}
		
		// Check is song in cart
		if (!in_array($songId, $info['song_ids'])) {
			return false;
		}
		
		// Get song by id
		$song = Song::model()->getByIds(array($songId));
		if (empty($song[$songId])) {
			return false;
		}
		$song = $song[$songId];
		
		// Get song ids which need to stay
		$info['song_ids'] = array_diff($info['song_ids'], array($songId));
		
		// Get songs by album id
		$songs = Song::model()->getByAlbumId($song['album_id']);
		if (empty($songs)) {
			return false;
		}
		
		// Remove current song from song list
		unset($songs[$songId]);
		
		// Check is need to delete album from cart
		if (sizeof(array_intersect(array_keys($songs), $info['song_ids'])) == 0) {
			// Get album ids which need to stay
			$info['album_ids'] = array_diff($info['album_ids'], array($song['album_id']));
		}
		
		// Recalculate songs number
		$info['songs_number'] = sizeof($info['song_ids']);
		
		// Prepare cache key
		if (Yii::app()->user->isGuest) {
			$key = 'cart_by_token_' . $this->token;
		} else {
			$key = 'cart_by_user_' . Yii::app()->user->id;
		}
		
		// Store cart info
		if (!TRedis::instance('cart')->set($key, $info)) {
			return false;
		}
		
		// Cleare app cache
		MTCache::instance('cart')->delete('cart');
		
		return true;
	}
	
	/**
	 * Method to remove songs by albums
	 * 
	 * @param int $albumId
	 * @return mixed
	 */
	public function removeAlbum($albumId)
	{
		if (empty($albumId)) {
			return false;
		}
		
		// Get current cart info
		$info = $this->getInfo();
		if (empty($info)) {
			return false;
		}
		
		// Check is album in cart
		if (!in_array($albumId, $info['album_ids'])) {
			return false;
		}
		
		// Get songs by album id
		$songs = Song::model()->getByAlbumId($albumId);
		if (empty($songs)) {
			return false;
		}
		
		// Get song ids which need to stay
		$info['song_ids'] = array_diff($info['song_ids'], array_keys($songs));
		
		// Get album ids which need to stay
		$info['album_ids'] = array_diff($info['album_ids'], array($albumId));
		
		// Recalculate songs number
		$info['songs_number'] = sizeof($info['song_ids']);
		
		// Prepare cache key
		if (Yii::app()->user->isGuest) {
			$key = 'cart_by_token_' . $this->token;
		} else {
			$key = 'cart_by_user_' . Yii::app()->user->id;
		}
		
		// Store cart info
		if (!TRedis::instance('cart')->set($key, $info)) {
			return false;
		}
		
		// Cleare app cache
		MTCache::instance('cart')->delete('cart');
		
		return true;
	}
	
	/**
	 * Method to add songs to cart by album ids
	 * 
	 * @param array $albumIds
	 * @return mixed
	 */
	public function addAlbums($albumIds)
	{
		// Check ids
		if (empty($albumIds)) {
			return false;
		}
		
		// Get current cart info
		$info = $this->getInfo();
		if (empty($info)) {
			// Create new cart
			$info = array(
				'songs_number' => 0,
				'album_ids' => array(),
				'song_ids' => array()
			);
		}
		
		// Prepare album ids
		$albumIds = array_map('intval', $albumIds);
		
		// Get song ids by album ids
		$songIds = Song::model()->getIdsByAlbumIds($albumIds);
		
		// Get not added ids
		if (!empty($info['song_ids'])) {
			$songIds = array_diff($songIds, $info['song_ids']);
			if (empty($songIds)) {
				return 0;
			}
		}
		
		// Add song ids
		$info['song_ids'] = array_merge($songIds, $info['song_ids']);
		
		// Add album ids
		$info['album_ids'] = !empty($info['album_ids']) ? array_unique(array_merge($albumIds, $info['album_ids'])) : $albumIds;
		
		// Recalculate songs number
		$info['songs_number'] = sizeof($info['song_ids']);
		
		// Prepare cache key
		if (Yii::app()->user->isGuest) {
			$key = 'cart_by_token_' . $this->token;
		} else {
			$key = 'cart_by_user_' . Yii::app()->user->id;
		}
		
		// Store cart info
		if (!TRedis::instance('cart')->set($key, $info)) {
			return false;
		}
		
		// Cleare app cache
		MTCache::instance('cart')->delete('cart');
		
		// Return number of added songs
		return sizeof($songIds);
	}
	
	/**
	 * Method to add songs to cart by them ids
	 * 
	 * @param array $songIds
	 * @return mixed
	 */
	public function addSongs($songIds)
	{
		// Check ids
		if (empty($songIds)) {
			return false;
		}
		
		// Get current cart info
		$info = $this->getInfo();
		if (empty($info)) {
			// Create new cart
			$info = array(
				'songs_number' => 0,
				'album_ids' => array(),
				'song_ids' => array()
			);
		}
		
		// Get not added ids
		if (!empty($info['song_ids'])) {
			$songIds = array_diff($songIds, $info['song_ids']);
			if (empty($songIds)) {
				return 0;
			}
		}
		
		// Get songs info
		$songs = Song::model()->getByIds($songIds);
		if (empty($songs)) {
			return false;
		}
		
		// Process songs
		foreach ($songs as $song) {
			// Add album id
			if (!isset($info['album_ids'][$song['album_id']])) {
				$info['album_ids'][$song['album_id']] = $song['album_id'];
			}
			
			// Add song id
			$info['song_ids'][$song['id']] = $song['id'];
		}
		
		// Recalculate songs number
		$info['songs_number'] = sizeof($info['song_ids']);
		
		// Prepare cache key
		if (Yii::app()->user->isGuest) {
			$key = 'cart_by_token_' . $this->token;
		} else {
			$key = 'cart_by_user_' . Yii::app()->user->id;
		}
		
		// Store cart info
		if (!TRedis::instance('cart')->set($key, $info)) {
			return false;
		}
		
		// Cleare app cache
		MTCache::instance('cart')->delete('cart');
		
		// Return number of added songs
		return sizeof($songIds);
	}
	
	/**
	 * Method to cleare current cart
	 * 
	 * @return bool
	 */
	public function cleare()
	{
		// Prepare cache key
		if (Yii::app()->user->isGuest) {
			$key = 'cart_by_token_' . $this->token;
		} else {
			$key = 'cart_by_user_' . Yii::app()->user->id;
		}
		
		return TRedis::instance('cart')->delete($key);
	}
	
	/**
	 * Method return info about current user cart
	 * 
	 * @return mixed
	 */
	public function getInfo()
	{
		// Check app cache
		if (!MTCache::instance('cart')->test('cart')) {
			// Get info from cache by token
			$tokenInfo = TRedis::instance('cart')->get('cart_by_token_' . $this->token);
			
			// Check is user was authorized
			if (!Yii::app()->user->isGuest) {
				$info = TRedis::instance('cart')->get('cart_by_user_' . Yii::app()->user->id);
				
				// Merge info
				if (!empty($tokenInfo)) {
					$info['song_ids'] = !empty($info['song_ids']) ? array_unique(array_merge($tokenInfo['song_ids'], $info['song_ids'])) : $tokenInfo['song_ids'];
					$info['album_ids'] = !empty($info['album_ids']) ? array_unique(array_merge($tokenInfo['album_ids'], $info['album_ids'])) : $tokenInfo['album_ids'];
					$info['songs_number'] = sizeof($info['song_ids']);
					
					// Delete cart by token
					TRedis::instance('cart')->delete('cart_by_token_' . $this->token);
					
					// Save cart by user id
					TRedis::instance('cart')->set('cart_by_user_' . Yii::app()->user->id, $info);
				}
			} else {
				$info = $tokenInfo;
			}
			
			if (!empty($info)) {
				// Store into app cache
				MTCache::instance('cart')->set('cart', $info);
				
				// Return info
				return $info;
			}
		} else {
			// Get info from app cache
			return MTCache::instance('cart')->get('cart');
		}
	}
	
	/**
	 * Method to get class instance
	 * 
	 * @return MTCart
	 */
	public static function instance()
	{
		// Check instance
		if (!empty(self::$instance)) {
			return self::$instance;
		}
		
		// Create new instance
		self::$instance = new MTCart();
		
		// Set current user token
		self::$instance->token = Yii::app()->getController()->getTmpUserToken();
		
		return self::$instance;
	} 
}