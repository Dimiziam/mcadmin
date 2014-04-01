<?php
/**
 * NewAlbumStatistic
 * 
 * Model for table new_album_statisitcs
 * 
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class NewAlbumStatistic extends InfoModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * 
	 * @param string $className active record class name.
	 * @return Resource the static model class
	 */
	public static function model($className=__CLASS__)
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
		return 'new_album_statisitcs';
	}
	
	/**
	 * Returns the primary key of the associated database table.
	 * 
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}
	
	/**
	 * Method to cleare current model cache
	 * 
	 * @return bool
	 */
	public function cleareCache()
	{
		return TRedis::instance('data')->deleteByTag('NEW_ALBUM_STATISTICS');
	}
	
	/**
	 * Method return icalendar info
	 * 
	 * @param string $currentDate
	 * @return mixed
	 */
	public function getForCalendar($currentDate)
	{
		if (empty($currentDate)) {
			return false;
		}
		
		// Create entity of current date
		$current = new DateTime($currentDate);
		
		// Try to get statisitcs from cache
		$statisitcs = TRedis::instance('data')->get('new_album_statisitcs_' . $current->format('Y-m'));
		
		if (empty($statisitcs)) {
			// Get statisitcs from DB
			$statisitcs = $this->getDbConnection()
				->createCommand('select * from ' . $this->tableName() . ' where year = :year and month = :month')
				->bindParam(":year", $current->format('Y'), PDO::PARAM_INT)
				->bindParam(":month", $current->format('n'), PDO::PARAM_INT)
				->queryAll();
			if (empty($statisitcs)) {
				return false;
			}
			
			// Get transalation for text
			$translation = MTLang::t('title_for_calendar', 'album');
			
			// Prepare statisitcs
			$prepared = array();
			foreach ($statisitcs as $statisitc) {
				// Prepare date params
				$params = array(
					'year' => $statisitc['year'],
					'month' => $statisitc['month'] <= 9 ? '0' . $statisitc['month'] : $statisitc['month'],
					'day' => $statisitc['day'] <= 9 ? '0' . $statisitc['day'] : $statisitc['day'],
				);
				
				$prepared[$params['year'] . '-' . $params['month'] . '-' . $params['day']] = sprintf($translation, $statisitc['albums_number']);
			}
			$statisitcs = $prepared;
			
			// Store statisitcs into cache
			TRedis::instance('data')->setByTag('NEW_ALBUM_STATISTICS', 'new_album_statisitcs_' . $current->format('Y-m'), $statisitcs);
		}
		
		return $statisitcs;
	}
	
	/**
	 * Method to recalculate statistics
	 * 
	 * @return bool
	 */
	public function recalculate()
	{
		// Truncate table
		try {
			$this->getDbConnection()->createCommand()->truncateTable($this->tableName());
		} catch (Exception $e) {
			return false;
		}
		
		// Recalculate statistics
		$result = $this->getDbConnection()->createCommand(
			'insert ignore into ' . $this->tableName() . ' (year, month, day, albums_number)
			select DATE_FORMAT(created, "%Y"), DATE_FORMAT(created, "%m"), DATE_FORMAT(created, "%d"), count(id)
			from ' . Album::model()->tableName() . ' 
			group by created'
		)->execute();
		
		// Cleare cache
		$this->cleareCache();
		
		return $result;
	}
}