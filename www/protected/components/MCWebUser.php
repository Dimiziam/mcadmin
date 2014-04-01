<?php
/**
 * MCWebUser
 * 
 * Component to work with web admin
 * 
 * @package MediaClub
 * @subpackage components
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MCWebUser extends CWebUser {
	
	/**
	 * Method to cleare cache for current admin
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return Admin::model()->cleareCache($this->getId());
	}
	
	/**
	 * Method return info about current admin
	 * 
	 * @return mixed
	 */
	public function getInfo()
	{
		// Check admin id
		if (!$this->getId()) {
			return false;
		}
		
		return Admin::model()->getById($this->getId());
	}
}