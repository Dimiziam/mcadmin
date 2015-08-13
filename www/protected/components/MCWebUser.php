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

	private $_model = null;

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




	function getRole() {
		if($user = $this->getModel()){
			// в таблице User есть поле role
			return $user->role;
		}
	}

	private function getModel(){
		if (!$this->isGuest && $this->_model === null){
			$this->_model = Admin::model()->findByPk($this->id, array('select' => 'role'));
		}
		return $this->_model;
	}

}