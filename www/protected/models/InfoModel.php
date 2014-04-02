<?php
/**
 * InfoModel
 * 
 * Parent class for all info DB models
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class InfoModel extends CActiveRecord
{
	/**
	 * Variable to store result message
	 * 
	 * @var array
	 */
	private $_resultMessage = array();
	
	/**
	 * Method return current row per page
	 * 
	 * @return int
	 */
	public function getCurrentPage()
	{
		return !empty($_POST[Yii::app()->params['pager']['variable']['current']]) ? (int) $_POST[Yii::app()->params['pager']['variable']['current']] - 1 : 0;
	}
	
	/**
	 * Method return current row per page
	 * 
	 * @return int
	 */
	public function getRowsPerPage()
	{
		if (!empty($_POST[Yii::app()->params['pager']['variable']['rpp']])) { 
			return (int) $_POST[Yii::app()->params['pager']['variable']['rpp']];
		} else {
			return !empty(Yii::app()->params['pager']['rpp']) ? Yii::app()->params['pager']['rpp'] : 20;
		}
	}
	
	/**
	 * Method to add result message
	 * 
	 * @param string $code
	 * @param string $message
	 * @return void
	 */
	public function addResult($code, $message)
	{
		$this->_resultMessage = array(
			'code' => $code,
			'message' => $message
		);
	}
	
	/**
	 * Method to get result message
	 * 
	 * @return array
	 */
	public function getResult()
	{
		return $this->_resultMessage;
	}
	
	/**
	 * Returns the database connection used by active record.
	 * By default, the "db" application component is used as the database connection.
	 * You may override this method if you want to use a different database connection.
	 * @return CDbConnection the database connection used by active record.
	 */
	public function getDbConnection()
	{
		// Check app cache
		if (MTCache::instance('connections')->test('info_db')) {
			// Get connection form app cache
			return MTCache::instance('connections')->get('info_db');
		}
		
		// Connect to DB
		$infoDb = new CDbConnection(
			Yii::app()->params['db']['info_db']['connectionString'],
			Yii::app()->params['db']['info_db']['username'],
			Yii::app()->params['db']['info_db']['password']
		);
		
		// Check connection
		if($infoDb instanceof CDbConnection) {
			// Set attributes
			if (!empty(Yii::app()->params['db']['info_db']['attributes'])) {
				// Process attributes
				foreach (Yii::app()->params['db']['info_db']['attributes'] as $key => $value) {
					$infoDb->{$key} = $value;
				}
			}
			
			// Store connection into app cache
			MTCache::instance('connections')->set('info_db', $infoDb);
			
			return $infoDb;
		} else {
			throw new CDbException(Yii::t('yii', 'Info DB model requires a "db" CDbConnection application component.'));
		}
	}
}
