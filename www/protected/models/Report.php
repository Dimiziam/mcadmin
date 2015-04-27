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
	 * Method to get OsaReport
	 */
	public function getOsaReport($dateFrom, $dateTo)
	{
		//if in config not set
		// do not word now
		//$this->setDefaultPerPage(2000);

		if ($this->_validate($dateFrom, $dateTo)) {

			// Try to find orders
			$command = $this->_prepareRepQuery();

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

		$command = $this->_prepareRepQuery(false);
		$items = $command->queryAll();


		$csvFileName = 'Report_OSA+' . $this->dateFrom . '_to_' . $this->dateTo . ".csv";


		$headerFilds = "OBDOBI;DRUH_SOUBORU;ISRC;ID_OSA;TITUL;AUTHOR_LIST;ALBUM;VYDAVATEL;INTERPPRET;NO_DOWNLOADS;KONC_CENA;TRZBA_CELKEM_AO_JEDNOTKA;AO_MINIMUM;AO_CELKEM\n";

		$outPutDada = $headerFilds;

		foreach ($items as $item) {

			$outPutDada .= date("Y-m-d", strtotime($item['created'])) . ";"
				. "skladba" . ";"
				. "-" . ";"
				. "-" . ";"
				. $item['song_title'] . ";"
				. "-" . ";"
				. $item['album_title'] . ";"
				. $item['lable'] . ";"
				. $item['singer_name'] . ";"
				. $item['download'] . ";"
				. $item['price'] . ";"
				. $item['cost'] . ";"
				. "-" . ";"
				. "-" . ";"
				. "-" . "\n";

		}

		// header
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=" . $csvFileName);
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
		$this->dateFrom = CDateTimeParser::parse($dateFrom, $this->format, array('month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0));
		$this->dateTo = CDateTimeParser::parse($dateTo, $this->format, array('month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0));

		if ($this->dateFrom && $this->dateTo) {
			return true;
		}

		return false;
	}

	/**
	 * Method to prepare Query for report
	 * @param bool $needLimit if need limit default true
	 * @return CDbCommand
	 */

	private function _prepareRepQuery($needLimit = true)
	{

		// prepare query
		// between criteria
		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('os.created', date("Y-m-d", $this->dateFrom), date("Y-m-d", $this->dateTo));
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
									os.price as price,
									count(os.id) AS download,
									sum(price) AS cost");
		$command->from("order_songs os");
		$command->join("singers s", "s.id = os.singer_id");
		$command->join("users u", "u.id = os.user_id");
		$command->join("owners o", "o.id = os.album_owner_id");
		$command->join("albums albm", "albm.id = os.album_id");

		$command->join("songs sg", " sg.id = os.song_id")
			->where("u.developer='no' AND o.rule_number=0")
			->andWhere($criteria->condition, $criteria->params);

		// if we need limit, default yes
		if ($needLimit) {
			$command->limit($this->getRowsPerPage(), ($this->getCurrentPage() * $this->getRowsPerPage()));
		}

		$command->order("os.created ASC")
			->group("os.song_id");
		return $command;
	}


}







