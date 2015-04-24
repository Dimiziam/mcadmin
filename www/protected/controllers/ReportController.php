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
				'actions' => array('search', 'add', 'edit', 'delete', 'index', 'osa'),
				'users' => array('@')
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
		$osaReportData = Report::model()->getOsaReport($dateFrom, $dateTo);
		$this->render('osa', array(
				'osaReportData' => $osaReportData,
				'dateFrom' => $dateFrom,
				'dateTo' => $dateTo,
			)
		);

	}


	public function actionOsa()
	{


		$this->actionIndex('osa');
	}


} 