<?php
/**
*
* @author Roman A. Shtempeliuk (deRamzes@gmail.com)
 * date  25.09.2013
 * @version  1.0
 */




/**
 * Profile form
 */


class ProfileForm extends CFormModel {


//form filds
	public $password;
	public $password_confirm;
	//public $email; - can't change it's login
	public $RealName;
	public $LastName;
	public $email;
	// hiidden
	public $myaction;



	// user id to update profile
	public  $user_id;





	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
				// username and password are required

				array('RealName, LastName', 'required'),
				// compare passwords
				array('password', 'compare', 'compareAttribute'=>'password_confirm'),
				array('password, password_confirm', 'length', 'min'=>6, 'max'=>40),
				array('myaction','safe')

		);
	}

/*
 * update profile
 */

	public function updateProfile() {





		$profile_data = array ('LastName' => $this->attributes['LastName'],
								'RealName' => $this->attributes['RealName']


		);
		// users model
		$user_model = Users::model()->findByPk($this->user_id);


		 $user_model->scenario='updateProfile';
		 $user_model->password = $this->attributes['password'];

		 //$data->LastName= $this->attributes['LastName'];
		 $user_model->setAttributes($profile_data);

			if  ($user_model->save()) {
				return true;
			}

		return false;

	}



}