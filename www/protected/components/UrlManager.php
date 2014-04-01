<?php
/**
 * Panel
 * 
 * Component to extends standart URL Manager
 * 
 * @package MediaClub
 * @subpackage components
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
class UrlManager extends CUrlManager {

	/**
	 * Constructs a URL.
	 * @param string $route the controller and the action (e.g. article/read)
	 * @param array $params list of GET parameters (name=>value). Both the name and value will be URL-encoded.
	 * If the name is '#', the corresponding value will be treated as an anchor
	 * and will be appended at the end of the URL.
	 * @param string $ampersand the token separating name-value pairs in the URL. Defaults to '&'.
	 * @return string the constructed URL
	 */
	public function createUrl($route, $params = array(), $ampersand = '&') {
		// Add current language
		$params['language'] = Yii::app()->params['currentLanguage'];
		
		// Call parent method
		return parent::createUrl($route, $params, $ampersand);
	}
}