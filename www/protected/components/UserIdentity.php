<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	// userid
	private  $_id;


	/**
	 *
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		$record = Users::model ()->findByAttributes ( array ('email' => $this->username ) );


		if ($record === null)
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		else if ($record->password !== utf8_encode ( crypt ( $this->password, $record->password ) )) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else {
			$this->username = $record->email;
			$this->_id=$record->id;
			$this->setState('role', $record->role);
			$this->errorCode = self::ERROR_NONE;
		}

		return ! $this->errorCode;
	}


	/**
	 * auth by email token - that send to appruve email
	 * only 1 time
	 * after succes auth cleanup token
	 *
	 * @param unknown $email_token
	 */
	public function authenticate_by_email_token($email_token) {
		// add check if not alredy emailchecked

		if ($email_token=='0' or $email_token==0) {
			$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
			return ! $this->errorCode;
		}

		$criteria=New CDbCriteria;
		$critieria->condition='EmailConfirm=0';

		$record = Users::model ()->findByAttributes (
					array ('approve_token_mail' => $email_token ),
					array(
						'condition'=>'EmailConfirm=:EmailConfirm',
						'params'=>array(':EmailConfirm'=>1)
						)

			 );
		// for zerofiling approve_token_mail
		//echo  $email_token;

		$record->scenario = 'update';
		if ($record === null)
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		else if ($record->approve_token_mail !== $email_token) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else {


			$this->username = $record->email;
			$this->_id=$record->id;
			$this->setState('role', $record->role);
			$this->errorCode = self::ERROR_NONE;
			// zerofiling
			/*

			$record->approve_token_mail=0;
			$record->save();
			*/
		}
		return !$this->errorCode;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getName()
	{
		return $this->username;
	}
		//

	public function GetAmount() {

		return 123;
	}
}