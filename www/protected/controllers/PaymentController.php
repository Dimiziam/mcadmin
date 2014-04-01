<?php
/**
 * PaymentController
 * 
 * Controller to work with payments
 * 
 * @package MediaClub
 * @subpackage controllers
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class PaymentController extends Controller {
	/**
	 * Specifies the access control rules.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('transactions', 'topup', 'topupReturn'),
				'users' => array('@')
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	/**
	 * Action to return user after payment
	 * 
	 * @param string $billing
	 * @param string $status
	 * @param int $transactionId
	 * @return void
	 */
	public function actionTopupReturn($billing, $status, $transactionId)
	{
		// Get order translations
		$translations = MTLang::instance()->getGroup('transaction');
		
		// Check is billing supports
		if (!MBPayPal::check($billing)) {
			Yii::app()->user->setFlash('error', sprintf($translations['unsupport_billing'], $billing));
			return $this->render('topup_return');
		}
		
		// Cehck return URL status
		if ($status != 'success') {
			Yii::app()->user->setFlash('error', $translations['payment_was_canceled']);
			return $this->render('topup_return');
		}
		
		// Confirm payment
		$result = MBPayPal::instance($billing)->confirm($transactionId, !empty($_REQUEST['PayerID']) ? $_REQUEST['PayerID'] : 0);
		
		// Set error message
		if (!$result) {
			Yii::app()->user->setFlash('error', MTLang::t(MBPayPal::instance($billing)->getError(), $billing));
		} else {
			Yii::app()->user->setFlash('success', $translations['payment_success']);
		}
		
		$this->render('topup_return', array('result' => $result));
	}
	
	/**
	 * Action to topup user account
	 * 
	 * @return void
	 */
	public function actionTopup()
	{
		if (!empty($_POST['amount'])) {
			// Create payment
			$result = MBPayPal::instance('PayPal')->create($_POST['amount']);
			
			// Set error message
			if (!$result) {
				Yii::app()->user->setFlash('error', MTLang::t(MBPayPal::instance('PayPal')->getError(), 'paypal'));
			}
			
			$this->render('topup', 
				array(
					'result' => $result,
					'amount' => $_POST['amount']
				)
			);
		} else {
			$this->render('topup');
		}
	}
	
	/**
	 * Action to display list of current user transactions
	 * 
	 * @return void
	 */
	public function actionTransactions()
	{
		// Get info and render view
		$this->render('transactions', 
			array(
				'transactions' => Transaction::model()->getByUserId(Yii::app()->user->id)
			)
		);
	}
}