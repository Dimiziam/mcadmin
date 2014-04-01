<?php
/**
 * MCMailer
 * 
 * Component to send emails
 * 
 * @package MediaClub
 * @subpackage Components
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MCMailer {
	
	const ERROR_RENDERING = 1;
	const ERROR_EMPTY_FROM = 2;
	const ERROR_EMPTY_TO = 3;
	
	/**
	 * Variable to store controller entity
	 * 
	 * @var Controller
	 */
	private $_controller;
	
	/**
	 * Variable to store emails
	 * 
	 * @var array
	 */
	private $_emails = array();

	/**
	 * Variable to store email body
	 * 
	 * @var string
	 */
	private $_body;
	
	/**
	 * Variable to store email type
	 * 
	 * @var string
	 */
	private $_type = 'html';
	
	/**
	 * Variable to store error code
	 * 
	 * @var int
	 */
	private $_errorCode = 0;
	
	/**
	 * Variable to store error message
	 * 
	 * @var string
	 */
	private $_error;
	
	/**
	 * Class constructor
	 * 
	 * @param Controller $controller
	 * @return void
	 */
	public function __construct(Controller $controller)
	{
		$this->_controller = $controller;
	}
	
	/**
	 * Method return instance of mailer class
	 * 
	 * @param Controller $controller
	 * @return Mailer
	 */
	public static function get(Controller $controller)
	{
		return new MCMailer($controller);
	}
	
	/**
	 * Method to send email
	 * 
	 * @param string $subject
	 * @return bool
	 */
	public function send($subject)
	{
		// Check data
		if (empty($this->_emails['from'])) {
			$this->_errorCode = self::ERROR_EMPTY_FROM;
			$this->_error = 'Не задан email отправителя';
			return false;
		}
		if (empty($this->_emails['to'])) {
			$this->_errorCode = self::ERROR_EMPTY_TO;
			$this->_error = 'Не заданы email получателей';
			return false;
		}
		
		// Prepare to
		$to = join(', ', $this->_emails['to']);
		
		// Prepare headers
		$headers = array(
			"MIME-Version: 1.0\r\n",
			"Content-type: text/" . $this->_type . "; charset=UTF-8\r\n",
			"To: " . $to . "\r\n",
			"From: " . $this->_emails['from'] . "\r\n"
		);
		
		// Send email
		return mail($to, '=?UTF-8?B?' . base64_encode(trim($subject)) . '?=', trim($this->_body), implode($headers));
	}
	
	/**
	 * Method to set emails
	 * 
	 * @param string $from - sender email
	 * @param string|array $to - destination emails
	 * @return Mailer
	 */
	public function setEmails($from, $to)
	{
		$this->_emails['from'] = $from;
		$this->_emails['to'] = is_array($to) ? $to : array($to);
		
		return $this;
	}
	
	/**
	 * Method to get Xmail body by rendering view
	 * 
	 * @param string $type - mail type: html or text
	 * @param string $view
	 * @param array $data
	 * @return Mailer
	 */
	public function render($type, $view, $data = null)
	{
		// Prepare view path
		$path = 'emails/' . Yii::app()->params['currentLanguage'] . '/' . $view . ucfirst($type);
		
		// Render view
		$this->_body = $this->_controller->renderPartial($path, $data, true);
		
		// Check render result
		if (empty($this->_xml)) {
			$this->_errorCode = self::ERROR_RENDERING;
			$this->_error = 'Ошибка получения тела письма из теплейта: ' . $view;
		}
		
		return $this;
	}
	
	/**
	 * Method return error code
	 * 
	 * @return int
	 */
	public function getErrorCode()
	{
		return $this->_errorCode;
	}
	
	/**
	 * Method return error message
	 * 
	 * @return string
	 */
	public function getError()
	{
		return $this->_error;
	}
}