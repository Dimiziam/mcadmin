<?php

/**
 * Report
 *
 * Model report class
 *
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Report extends InfoModel
{


	/**
	 * dat format for validation
	 * @var string
	 */
	var $format = 'yyyy-MM-dd';

	private $dateFrom;
	private $dateTo;


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 * @return Resource the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Method return table name for current model
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'order_songs';
	}

	/**
	 * get payPal Transaction
	 * @param $dateFrom
	 * @param $dateTo
	 * @return bool|MTArrayDataProvider
	 * @throws CDbException
	 */
	public function getPayPalReport($dateFrom, $dateTo)
	{
		if ($this->_validate($dateFrom, $dateTo)) {

			// paypalPayments

			// prepare query
			// between criteria
			$criteria = new CDbCriteria();
			$criteria->addBetweenCondition('tr.date_start', $this->dateFrom, $this->dateTo);
			// get downloaded albums by date
			$command = $this->getDbConnection()
				->createCommand()->select("tr.id,
										  tr.date_end,
										  tr.status,
										  tr.info,
										  tr.amount,
										  u.email,
										  u.name,
										  u.last_name,
										  u.country_code", 'SQL_CALC_FOUND_ROWS');

			$command->from("transactions tr");
			$command->leftJoin("users u", "u.id = tr.user_id")
				->where("tr.status='done'")
				->andWhere("tr.billing='paypal'")
				->andWhere($criteria->condition, $criteria->params);



			if (!empty($_POST['show_only_eu'])) {
				$country_codes = join(Yii::app()->params['EU_codes'],"','");
				$command->andWhere("u.country_code IN ('$country_codes')");
			}


			// if we need limit, default yes
			$command->limit($this->getRowsPerPage(), ($this->getCurrentPage() * $this->getRowsPerPage()));

			$command->order("tr.date_start ASC");


			$reportData = $command->queryAll();

			// Check orders
			if (empty($reportData)) {
				return false;
			}

			// Get nuber of found rows
			$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();

			// Get data provider
			$provider = new MTArrayDataProvider($reportData,
				array(
					'totalItemCount' => $foundRows,
					'pagination' => array('pageSize' => $this->getRowsPerPage()),
				)
			);
			// Set offset
			$provider->setOffset(0);
			return $provider;
		}
		return false;

	}

	/**
	 * Get PayU filtered transaction
	 * @param $dateFrom
	 * @param $dateTo
	 * @return bool|MTArrayDataProvider
	 * @throws CDbException
	 */
	public function getPayUReport($dateFrom, $dateTo)
	{
		if ($this->_validate($dateFrom, $dateTo)) {

			// paypalPayments

			// prepare query
			// between criteria
			$criteria = new CDbCriteria();
			$criteria->addBetweenCondition('tr.date_start', $this->dateFrom, $this->dateTo);
			// get downloaded albums by date
			$command = $this->getDbConnection()
				->createCommand()->select("tr.id,
										  tr.date_end,
										  tr.status,
										  tr.info,
										  tr.amount,
										  u.email,
										  u.name,
										  u.last_name,
										  u.country_code", 'SQL_CALC_FOUND_ROWS');

			$command->from("transactions tr");
			$command->leftJoin("users u", "u.id = tr.user_id")
				->where("tr.status='done'")
				->andWhere("tr.billing='payu'")
				->andWhere($criteria->condition, $criteria->params);



			if (!empty($_POST['show_only_eu'])) {
				$country_codes = join(Yii::app()->params['EU_codes'],"','");
				$command->andWhere("u.country_code IN ('$country_codes')");
			}


			// if we need limit, default yes
			$command->limit($this->getRowsPerPage(), ($this->getCurrentPage() * $this->getRowsPerPage()));

			$command->order("tr.date_start ASC");


			$reportData = $command->queryAll();

			// Check orders
			if (empty($reportData)) {
				return false;
			}

			// Get nuber of found rows
			$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();

			// Get data provider
			$provider = new MTArrayDataProvider($reportData,
				array(
					'totalItemCount' => $foundRows,
					'pagination' => array('pageSize' => $this->getRowsPerPage()),
				)
			);
			// Set offset
			$provider->setOffset(0);
			return $provider;
		}
		return false;

	}




	/**
	 * Method to get OsaReport
	 */
	public function getOsaReport($dateFrom, $dateTo)
	{
		//if in config not set
		// do not word now
		//$this->setDefaultPerPage(2000);

		if ($this->_validate($dateFrom, $dateTo)) {

			// Try to find orders
			$command = $this->_prepareOsaQuery();

			$reportData = $command->queryAll();

			// Check orders
			if (empty($reportData)) {
				return false;
			}

			// Get nuber of found rows
			$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();

			// Get data provider
			$provider = new MTArrayDataProvider($reportData,
				array(
					'totalItemCount' => $foundRows,
					'pagination' => array('pageSize' => $this->getRowsPerPage()),
				)
			);
			// Set offset
			$provider->setOffset(0);
			return $provider;
		}
		return false;

	}

	/**
	 * Method to Get Osa report in CSV
	 * @param $dateFrom
	 * @param $dateTo
	 * @return file
	 */

	public function getOsaReportAsCSV($dateFrom, $dateTo)
	{

		$this->_validate($dateFrom, $dateTo);

		$command = $this->_prepareOsaQuery(false);
		$items = $command->queryAll();

		$csvFileName = 'Report_OSA+' . $this->dateFrom . '_to_' . $this->dateTo . ".csv";
		//hedaer filds
		$headerFilds = MTLang::t("reportCSVHeader", "report") . "\n";
		$outPutDada = $headerFilds;

		foreach ($items as $item) {

			$price = round($item['price'] * Yii::app()->request->getParam('currency'), 2);
			$cost = round($item['cost'] * Yii::app()->request->getParam('currency'), 2);
			$to_pay = $price * Yii::app()->params['AO_ednotka'];

			if ($to_pay < Yii::app()->params['AO_minimup']) {
				$new_pay = Yii::app()->params['AO_minimup'] * $item['download'];
			} else {
				$new_pay = $price * $item['download'];
			}


			$outPutDada .= date("Ym", strtotime($item['created'])) . ";"
				. "SKLADBA" . ";"
				. "" . ";"
				. "" . ";"
				. "\"" . $item['song_title'] .  "\"" . ";"
				. "" . ";"
				. "\"" . $item['album_title'] .  "\"" .  ";"
				. "\"" . $item['lable'] . "\"" . ";"
				. "\"" . $item['singer_name'] . "\"" . ";"
				. $item['download'] . ";"
				. "\"" . number_format($price, 2, ',', '') . "\"" . ";"
				. "\"" . number_format($cost, 2, ',', '') . "\"" . ";"
				. "\"" . number_format($to_pay, 2, ',', '') . "\"" . ";"
				. "\"" . number_format(Yii::app()->params['AO_minimup'], 2, ',', '') . "\"" . ";"
				. "\"" . number_format($new_pay, 2, ',', '') . "\"" . "\n";

		}


		$outPutDada = html_entity_decode(mb_convert_encoding($outPutDada, "windows-1251", "utf-8"),ENT_HTML401,'cp1251');
		// header
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachm`ent; filename=" . $csvFileName);
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: binary");

		echo $outPutDada;


		Yii::app()->end();
	}


	/**
	 * Method to validate and set dates
	 * @param date $dateFrom ;
	 * @param date $dateTo ;
	 * @return bool
	 *
	 */
	private function _validate($dateFrom, $dateTo)
	{
		$dateFrom = CDateTimeParser::parse($dateFrom, $this->format, array('month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0));
		$dateTo = CDateTimeParser::parse($dateTo, $this->format, array('month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0));

		if ($dateFrom && $dateTo) {

			$this->dateFrom = date("Y-m-d", $dateFrom);
			$this->dateTo = date("Y-m-d 23:59:59", $dateTo);

			return true;
		}

		return false;
	}

	/**
	 * Method to prepare Query for Osa report
	 * @param bool $needLimit if need limit default true
	 * @return CDbCommand
	 */

	private function _prepareOsaQuery($needLimit = true)
	{

		// prepare query
		// between criteria
		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('os.created', $this->dateFrom, $this->dateTo);
		// get downloaded albums by date
		$command = $this->getDbConnection()
			->createCommand()->select(" SQL_CALC_FOUND_ROWS os.*,
									s.name AS singer_name,
									sg.name AS song_title,
									u.email,
									albm.title AS album_title,
									u.developer,
									u.download_all,
									o.name AS lable,
									o.rule_number,
									s.permit_from,
									os.price as price,
									count(os.id) AS download,
									sum(price) AS cost");
		$command->from("order_songs os");
		$command->join("singers s", "s.id = os.singer_id");
		$command->join("users u", "u.id = os.user_id");
		$command->join("owners o", "o.id = os.album_owner_id");
		$command->join("albums albm", "albm.id = os.album_id");

		$command->join("songs sg", " sg.id = os.song_id")
			->where("u.developer='no' AND o.rule_number=0 and s.permit_from=0")
			->andWhere($criteria->condition, $criteria->params);

		// if we need limit, default yes
		if ($needLimit) {
			$command->limit($this->getRowsPerPage(), ($this->getCurrentPage() * $this->getRowsPerPage()));
		}

		$command->order("os.created ASC")
			->group("os.song_id");
		return $command;


	}

	/**
	 * Method to get data amount by day
	 * @param string $type paypal|payu
	 * @return array
	 * @throws CDbException
	 */

	public function getDateForChart ($type='') {


		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('os.created', $this->dateFrom, $this->dateTo);
		// get downloaded albums by date
		$command = $this->getDbConnection()
			->createCommand()->select('UNIX_TIMESTAMP(DATE_FORMAT(t.date_start,"%Y-%m-01")) AS myadate, SUM(amount) amount ')
			->from('transactions t')
			->where("t.type='add'
					AND t.status = 'done'")
			->group('DATE_FORMAT(t.date_start,"%Y-%m")');


		if (!empty($type)) {
			$command->andWhere('t.billing=:billing')
			->bindParam(':billing', $type, PDO::PARAM_STR);
		}
		else {
			$command->andWhere('(t.billing="payu" or t.billing="paypal")');
		}


		$items = $command->queryAll();
		
		foreach ($items as $item) {
			$forJson[] = array((int)$item['myadate']*1000,(float)$item['amount']);
		}

		return $forJson;

	}


}







