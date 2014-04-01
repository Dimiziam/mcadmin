<?php
/**
*
* @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 * date  04.09.2013
 * @version  1.0
 */

// manipulate user data


class users extends CActiveRecord {

	/**
	 * The followings are the available columns in table 'users':
	 *
	 * @var integer $id
	 * @var string $email // used as login
	 * @var string $password
	 * @var string $Name
	 * @var string $LastName
	 * @var DateTime $created
	 * @var bool $EmailConfirm // true or false
	 * @var string $approve_token_mail
	 * @var string $FB_TOOKEN // facebook token for auth via facebook
	 */

	// role
	// not uset now
	const ROLE_ADMIN 	= 'administrator';
	const ROLE_MODER	= 'moderator';
	const ROLE_USER		= 'user';
	const ROLE_BANNED	= 'banned';


	// email token for rirsh auth
	public $approve_token_mail;


	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

 	/**
 	 * @return string the associated database table name
 	 */
 	public function tableName()
 	{
 		return '{{users}}';
 	}


 	/**
 	 * @return array validation rules for model attributes.
 	 */
 	public function rules() {
 		// NOTE: you should only define rules for those attributes that
 		// will receive user inputs.
 		return array(
 				array('email, password', 'required','on'=>'insert'),
 				//array('author, email, url', 'length', 'max'=>128),
 				array('email','unique','allowEmpty' => false, 'attributeName' => 'email', 'className' => 'users','on'=>'insert'),
 				//array('email','email'),
 				//array('url','url'),
 				array('created','default',
 						'value'=>new CDbExpression('NOW()'),
 						'setOnEmpty'=>false,'on'=>'insert'),
 				array('approve_token_mail','default',
 						'value'=>sha1(mt_rand(10000,99999).time()),
						'setOnEmpty'=>false,'on'=>'insert'),
 				array('LastName, RealName','safe')
 			);

 	}





 	// send Actiovation Mail

 	/**
 	 * Send activation mail
 	 * @return void
 	 * @param string $type
 	 */
 	public function send_activation_main($type='approve') {



 		$message = new YiiMailMessage;
 		$message->view = 'activation_mail';


 		$activation_url =Yii::app()->createAbsoluteUrl('/user/activation/',array(
 				"approve_token_mail" => $this->approve_token_mail, "email" => $this->email));

 		$message->setBody(array('activation_url'=>$activation_url), 'text/html');
 		$body = $message->message->getBody();
 		$message->subject = Yii::t('mail','subject_activate_mail');

 		//echo $this->email;
 		$message->addTo($this->email);
 		$message->from   = (Yii::app()->params['mailer']);
 		Yii::app()->mail->send($message);


 	}





 	/**
 	 * do automatic login user after approve e-mail
 	 * zerofil approve_token_mail after first success login
 	 *
 	 * if succes forward to profile
 	 * @return void
 	 */
 	public function automaticlogin(){


		// in case automaticlogin by mail_token
		$identity = new UserIdentity('auto','auto');
 		// auth
		//echo 'login1';
		//echo $this->approve_token_mail;
		$identity->authenticate_by_email_token($this->approve_token_mail);

 		if ($identity->authenticate_by_email_token($this->approve_token_mail)) {

 			if (Yii::app()->user->name) {
 				//alredy auth
				}
				else {
		 			 Yii::app()->user->login($identity,60*60*24);
				}

		}
		else {
			echo $identity->errorMessage;
		}
//print_r(Yii::app()->user->isGuest);
	//
/*
// normal auth use later
		echo '<pre>';
 		print_R($_SESSION);

		$identity=new UserIdentity($this->email,'1234567');
 		if($identity->authenticate())
 			{
 				Yii::app()->user->login($identity);
 				var_dump(Yii::app()->user->name);
 				var_dump(Yii::app()->session->sessionID);
 			}
 			else
 				echo $identity->errorCode;
*/

 	}

 	/**
 	 * (non-PHPdoc)
 	 * @see CActiveRecord::beforeSave()
 	 */
	public function beforeSave() {


			if($this->isNewRecord)
			{
			// если чтото в пароле нам надо его шифрануть
				if (!empty($this->password)) {
					// passwd don't empty generate hash
					$this->password = crypt($this->password, Randomness::blowfishSalt());
				}

				$this->role = self::ROLE_USER;

			}


			if ($this->scenario=='updateProfile' && !empty($this->password)) {
				$this->password = crypt($this->password, Randomness::blowfishSalt());
			}



		return 	parent::beforeSave();
	}


}
