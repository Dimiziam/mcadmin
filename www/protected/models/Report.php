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

		// validate date
		$dateFrom = CDateTimeParser::parse($dateFrom, $this->format, array('month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0));
		$dateTo = CDateTimeParser::parse($dateTo, $this->format, array('month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0));

		if ($dateFrom && $dateTo) {
			// between criteria
			$criteria = new CDbCriteria();
			$criteria->addBetweenCondition('os.created', date("Y-m-d",$dateFrom), date("Y-m-d",$dateTo));


			// get downloaded albums by date
			$command = $this->getDbConnection()
				->createCommand()->select(" SQL_CALC_FOUND_ROWS os.*,
									s.name AS singer_name,
									sg.name AS song_title,
									u.email,
									u.developer,
									u.download_all,
									o.name AS lable,
									o.rule_number,
									count(os.id) AS download,
									sum(price) AS cost");
			$command->from("order_songs os");
			$command->join("singers s", "s.id = os.singer_id");
			$command->join("users u", "u.id = os.user_id");
			$command->join("owners o", "o.id = os.album_owner_id");
			$command->join("songs sg", " sg.id = os.song_id")
				//->where("u.developer='no' AND o.rule_number=0")
				//->andWhere($criteria->condition, $criteria->params)
				->limit($this->getRowsPerPage(), ($this->getCurrentPage() * $this->getRowsPerPage()))
				->order("os.created ASC")
				->group("os.song_id");

			// Try to find orders
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


}


