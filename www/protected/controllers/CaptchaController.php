<?php
/**
 * CaptchaController
 * 
 * Controller to work with captcha
 * 
 * @package MediClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class CaptchaController extends Controller
{
	public $layout = 'captcha';
	
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('display'),
				'users' => array('*')
			)
		);
	}

	/**
	 * Action to display captcha image
	 * 
	 * @param string $key
	 * @return void
	 */
	public function actionDisplay($key)
	{
		// Get captcha handler
		$captcha = new MTCaptcha('data', $this->getTmpUserToken());
		
		// Configure captcha
		$captcha->configure(
			array(
				'key' => $key,
				'width' => 150,
				'height' => 60,
				'font' => 'antiqua',
				'length' => 5,
				'allowedSymbols' => 'didgits'
			)
		);
		
		// Display image
		$captcha->render();
		
		// Exit
		Yii::app()->end();
	}
}