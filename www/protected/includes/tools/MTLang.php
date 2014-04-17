<?php
/**
 * MTLang
 * 
 * Class to work with static language translations
 * 
 * @package MediaClub
 * @subpackage tools
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MTLang
{
	/**
	 * Variable to store instance of class
	 * 
	 * @var MTLang
	 */
	public static $instance = null;

	/**
	 * Alias for not static method get()
	 * 
	 * @param string $alias
	 * @param string $group
	 * @param string $language
	 * @return mixed
	 */
	public static function t($alias, $group, $language = null)
	{
		return MTLang::instance()->get($alias, $group, $language);
	}
	
	/**
	 * Method to cleare transactions cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('lang')->deleteByTag('TRANSLATIONS')
			&& MTCache::instance('translations')->cleareCache();
	}
	
	/**
	 * Get translations by alias and group
	 * 
	 * @param string $alias
	 * @param string $group
	 * @param string $language
	 * @return mixed
	 */
	public function get($alias, $group, $language = null)
	{
		// Get translations by group
		$translations = $this->getGroup($group);
		if (empty($translations)) {
			return false;
		}
		
		return !empty($translations[$alias]) ? $translations[$alias] : false;
	}
	
	/**
	 * Get group of translations
	 * 
	 * @param string $group
	 * @param string $language
	 * @return mixed
	 */
	public function getGroup($group, $language = null)
	{
		// Prepare language alias
		$language = !empty($language) ? $language : Yii::app()->params['currentLanguage'];
		
		// Load translations for current language to cache
		if (!$this->loadToCache($language)) {
			// Check is current language is default language
			if ($language == Yii::app()->language) {
				return false;
			}
			
			// Try to load translations for default language
			if (!$this->loadToCache(Yii::app()->language)) {
				return false;
			}
			
			// Change current language to default
			$language = Yii::app()->language;
		}
		
		// Prepare cache key
		$key = $language . '_translations_' . $group;
		
		// Check app cache
		if (!MTCache::instance('translations')->test($key)) {
			// Get translations from Redis
			$translations = TRedis::instance('lang')->get($key);
			
			// Store into app cache
			MTCache::instance('translations')->set($key, $translations);
		} else {
			// Get translations from app cache
			$translations = MTCache::instance('translations')->get($key);
		}
		
		return $translations;
	}
	
	/**
	 * Method to find lang file and load it into cache
	 * 
	 * @param string $language
	 * @return bool
	 */
	public function loadToCache($language = null)
	{
		// Prepare language alias
		$language = !empty($language) ? $language : Yii::app()->params['currentLanguage'];
		
		// Check is translations already in cache
		if (TRedis::instance('lang')->get('is_translations_in_cache_' . $language)) {
			return true;
		}
		
		// Check path to language folder
		if (empty(Yii::app()->params['paths']['lang'])) {
			return false;
		}
		
		// Prepaer path to language file
		$path = Yii::app()->params['paths']['lang'] . '/' . $language . '.ini';
		
		// Check language file
		if (!file_exists($path) || filesize($path) == 0) {
			return false;
		}
		
		// Load translations
		$translations = parse_ini_file($path, true);
		if (empty($translations)) {
			return false;
		}
		
		// Process translations groups
		foreach ($translations as $groupAlias => $items) {
			// Store translations for current group
			if (!TRedis::instance('lang')->setByTag('TRANSLATIONS', $language . '_translations_' . $groupAlias, $items)) {
				return false;
			}
		}
		
		// Store cached flag
		return TRedis::instance('lang')->setByTag('TRANSLATIONS', 'is_translations_in_cache_' . $language, true);
	}
	
	/**
	 * Method to get class instance
	 * 
	 * @return MTLang
	 */
	public static function instance()
	{
		// Check instance
		if (!empty(self::$instance)) {
			return self::$instance;
		}
		
		// Create new instance
		return self::$instance = new MTLang();
	} 
}