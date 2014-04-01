<?php
/**
*
* @author Roman A. Shtempeliuk (deRamzes@gmail.com)
 * date  03.09.2013
 * @version  1.0
 */

/**
 * Register class.
 * RegForm is the data structure for keeping
 * user Register form data. It is used by the action in 'userController'.
 */
class RegForm extends CFormModel
{


	public $email;
	public $password;
	public $password_confirm;


	private $_identity;



	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
				// username and password are required
				array('password, password_confirm ', 'required'),
				array('email', 'email'),
				array('email','unique','allowEmpty' => false, 'attributeName' => 'email', 'className' => 'users'),
				// compare passwords
				array('password', 'compare', 'compareAttribute'=>'password_confirm'),
				array('password, password_confirm', 'length', 'min'=>6, 'max'=>40),
		);
	}



	// register method


	public function register() {

		// user model
		//print_r($this->attributes);
		$user_model = new Users();

		$user_data = array ('email' => $this->attributes ['email'],
							'password' => $this->attributes ['password']


		);

		$user_model->attributes = $user_data;

		$user_model->send_activation_main();
		/*
		if ($user_model->save()) {

			// send activation mail
			$user_model->send_activation_main();
			//send mail

		}
		*/
		return false;
	}






}