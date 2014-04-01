<?php
// Set path of aliases
Yii::setPathOfAlias('includes', dirname(__FILE__)."../includes");

// Import zebra pagination
Yii::import('application.includes.Zebra_Pagination');

/**
 * Panel
 * 
 * Widget to display pagination
 * 
 * @package MediaClub
 * @subpackage widgets
 * @copyright (C) 2014 <deRamzes@gmail.com>
 * @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 */
class Paginator extends CWidget
{
	/**
	 * Variable to store instance of CPagination
	 * 
	 * @var CPagination
	 */
	public $pages;

	/**
	 * Method to run widget
	 * 
	 * @return void
	 */
	public function run()
	{
		// Create instantiate the pagination object
		$pagination = new Zebra_Pagination();

		// Set selectedble pages
		$pagination->selectable_pages(7);

		// Set position of the next/previous page links
		$pagination->navigation_position(isset($_GET['navigation_position']) && in_array($_GET['navigation_position'], array('left', 'right')) ? $_GET['navigation_position'] : 'outside');

		// Set the number of total records is the number of records in the array
		$pagination->records($this->pages->getItemCount());

		// Set records per page
		$pagination->records_per_page($this->pages->getPageSize());

		// Set variable name
		$pagination->variable_name(Yii::app()->params['pager']['variable']);
		
		// Display pagination
		echo  $pagination->render();
	}
}
