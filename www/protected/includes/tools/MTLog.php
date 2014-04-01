<?php
/**
 * MTLog
 * 
 * Class to work with logs
 * 
 * @package MediaClub
 * @subpackage tools
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MTLog
{
	/**
	 * Variable to store instances of class
	 * 
	 * @var array
	 */
	public static $instance = array();
	
	/**
	 * Variable to store log prefix
	 * 
	 * @var string
	 */
	private $_prefix = '';
	
	/**
	 * Variable to store relative path
	 * 
	 * @var string
	 */
	private $_relativePath = '';
	
	/**
	 * Variable to store file handler
	 * 
	 * @var resource
	 */
	private $_fileHandler = null;
	
	/**
	 * Class constructor
	 * 
	 * @param string $prefix
	 * @return void
	 */
	public function __construct($prefix)
	{
		$this->_prefix = $prefix;
	}
	
	/**
	 * Method to create or open log file
	 * 
	 * @param string $relativePath
	 * @param string $mode
	 * @return bool
	 */
	public function start($relativePath, $mode = 'a+t')
	{
		// Close current file
		if (!empty($this->_fileHandler) && is_resource($this->_fileHandler)) {
			fclose($this->_fileHandler);
		}
		
		// Store relative path
		$this->_relativePath = $relativePath;
		
		// Prepar full path
		$path = Yii::app()->params['paths']['logs'] . '/' . $this->_prefix . '/' . $this->_relativePath;
		
		// Check is dir exists
		if (!file_exists(dirname($path)) && !mkdir(dirname($path), 0777, true)) {
			return false;
		}
		
		// Open file
		$this->_fileHandler = fopen($path, $mode);
		if (empty($this->_fileHandler) || !is_resource($this->_fileHandler)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Method to add message into log
	 * 
	 * @param string $message
	 * @return bool
	 */
	public function addMessage($message)
	{
		// Check file handler
		if (empty($this->_fileHandler) || !is_resource($this->_fileHandler)) {
			return false;
		}
		
		return fwrite($this->_fileHandler, $message . "\n\r");
	}
	
	/**
	 * Method to punt variable into log file
	 * 
	 * @param mixed $variable
	 * @param string $title
	 * @return bool
	 */
	public function addVariable($variable, $title = '')
	{
		// Add title
		if (!empty($title)) {
			$this->addMessage($title);
		}
		
		// Add message
		return $this->addMessage(print_r($variable, true));
	}
	
	/**
	 * Method return log path
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return Yii::app()->params['paths']['logs'] . '/' . $this->_prefix . '/' . $this->_relativePath;
	}
	
	/**
	 * Method return relative log path
	 * 
	 * @return string
	 */
	public function getRelativePath()
	{
		return $this->_relativePath;
	}
	
	/**
	 * Method to close log file
	 * 
	 * @return bool
	 */
	public function end()
	{
		return fclose($this->_fileHandler);
	}
	
	/**
	 * Method to get class instance
	 * 
	 * @param string $prefix
	 * @return MTLog
	 */
	public static function instance($prefix)
	{
		// Check instance
		if (!empty(self::$instance[$prefix])) {
			return self::$instance[$prefix];
		}
		
		// Create new instance
		return self::$instance[$prefix] = new MTLog($prefix);
	} 
}