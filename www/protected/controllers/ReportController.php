<?php

/**
 * ReportClass
 * Class to work with repors
 * @package admin.mediaclub.com.
 * Date: 23.04.2015
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */
class ReportController extends Controller
{

	/**
	 * Specifies the access control rules.
	 *
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('paypal', 'index', 'osa','payu'),
				'users' => array('@'),
				'roles'=>array('admin'),
			),
			array('deny',
				'users' => array('*'),
			)
		);
	}

	// index action
	public function actionIndex($report = 'osa')
	{

		$dateFrom = Yii::app()->request->getParam('dateFrom');
		$dateTo = Yii::app()->request->getParam('dateTo');

		if ($report == 'osa') {

			$getReport = Yii::app()->request->getParam('getReport');

			if (!empty($getReport)) {
				$this->logOff();
				Report::model()->getOsaReportAsCSV($dateFrom, $dateTo);
				return false;
			}

			$osaReportData = Report::model()->getOsaReport($dateFrom, $dateTo);
			$this->render('osa', array(
					'osaReportData' => $osaReportData,
					'dateFrom' => $dateFrom,
					'dateTo' => $dateTo,
				)
			);

			return false;
		}

		if ($report=='paypal') {

			$paypalReportData = Report::model()->getPayPalReport($dateFrom, $dateTo);

			//pc::debug($paypalReportData);
			$this->render('paypal', array(
					'paypalReportData' => $paypalReportData,
					'dateFrom' => $dateFrom,
					'dateTo' => $dateTo,
				)
			);

			return false;
		}

		if ($report=='payu') {

			$payuReportData = Report::model()->getPayUReport($dateFrom, $dateTo);


			$this->render('payu', array(
					'payUReportData' => $payuReportData,
					'dateFrom' => $dateFrom,
					'dateTo' => $dateTo,
				)
			);

			return false;
		}


	}


	public function actionOsa()
	{
		$this->actionIndex('osa');
	}

	public function actionPaypal()
	{
		$this->actionIndex('paypal');
	}

	public function actionPayU()
	{
		$this->actionIndex('payu');
	}
} 