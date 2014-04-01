<?php
/**
 * MTArrayDataProvider
 * 
 * Class which extends CArrayDataProvider
 * 
 * @package MediaClub
 * @subpackage tools
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MTArrayDataProvider extends CArrayDataProvider
{
	/**
	 * Variable to store array offset
	 * 
	 * @var int
	 */
	private $_offset = null;
	
	/**
	 * Fetches the data from the persistent data storage.
	 * @return array list of data items
	 */
	protected function fetchData()
	{
		if(($sort=$this->getSort())!==false && ($order=$sort->getOrderBy())!='')
			$this->sortData($this->getSortDirections($order));

		if(($pagination=$this->getPagination())!==false)
		{
			$pagination->setItemCount($this->getTotalItemCount());
			return array_slice($this->rawData, isset($this->_offset) ? $this->_offset : $this->getOffset(), $pagination->getLimit());
		}
		else
			return $this->rawData;
	}
	
	/**
	 * Method to set current offset
	 * 
	 * @param int $offset
	 * @return void
	 */
	public function setOffset($offset)
	{
		$this->_offset = $offset;
	}
}