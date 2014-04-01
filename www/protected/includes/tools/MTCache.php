<?php
/**
 * MTCache
 * 
 * Class to work with application runtime cache
 * 
 * @package MediaClub
 * @subpackage tools
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MTCache
{
	/**
	 * Variable to store instances of class
	 * 
	 * @var array
	 */
	public static $instance = array();

	/**
	 * Variable to store cache items
	 * 
	 * @var array
	 */
	public $items = array();
	
	/**
	 * Method to cleare transactions cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		$this->items = array();
		return true;
	}
	
	/**
	 * Method to delete value from cache
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function delete($key)
	{
		// Check is in cache
		if (!isset($this->items[$key])) {
			return true;
		}
		
		// Delete from cache
		unset($this->items[$key]);
		return true;
	}
	
	/**
	 * Method to check is value in cache
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function test($key)
	{
		return isset($this->items[$key]) ? true : false;
	}
	
	/**
	 * Method to get value from cache
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return isset($this->items[$key]) ? $this->items[$key] : false;
	}
	
	/**
	 * Method to store valute to cache
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value)
	{
		$this->items[$key] = $value;
	}
	
	/**
	 * Method to get class instance
	 * 
	 * @return MTCache
	 */
	public static function instance($prefix)
	{
		// Check instance
		if (!empty(self::$instance[$prefix])) {
			return self::$instance[$prefix];
		}
		
		// Create new instance
		return self::$instance[$prefix] = new MTCache();
	} 
}