<?php

/**
 * BackendSearchStatistics
 * This is the statistics-action, it will display the overview of search statistics
 *
 * @package		backend
 * @subpackage	search
 *
 * @author 		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendSearchStatistics extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		Spoon::dump(BackendPagesModel::get(1, 'nl'));
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrids
	 *
	 * @return void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendSearchModel::QRY_DATAGRID_BROWSE_STATISTICS, BL::getWorkingLanguage());

		// hide column
		$this->datagrid->setColumnsHidden('data');

		// create column
		$this->datagrid->addColumn('ip', BL::getLabel('IP'));
		$this->datagrid->addColumn('referrer', BL::getLabel('Referrer'));

		// header labels
		$this->datagrid->setHeaderLabels(array('time' => ucfirst(BL::getLabel('SearchedOn'))));

		// set column function
		$this->datagrid->setColumnFunction(array(__CLASS__, 'setIp'), '[data]', 'ip');
		$this->datagrid->setColumnFunction(array(__CLASS__, 'setReferrer'), '[data]', 'referrer');
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[time]'), 'time', true);

		// sorting columns
		$this->datagrid->setSortingColumns(array('time', 'term'), 'time');
		$this->datagrid->setSortParameter('desc');
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}


	/**
	 * Set column ip
	 *
	 * @return	string
	 * @param	string $data	The source data.
	 */
	public static function setIp($data)
	{
		// unserialize
		$data = unserialize($data);

		// return correct data
		return (isset($data['server']['HTTP_X_FORWARDED_FOR'])) ? $data['server']['HTTP_X_FORWARDED_FOR'] : $data['server']['REMOTE_ADDR'];
	}


	/**
	 * Set column referrer
	 *
	 * @return	string
	 * @param	string $data	The source data.
	 */
	public static function setReferrer($data)
	{
		// unserialize
		$data = unserialize($data);

		// return correct data
		return (isset($data['server']['HTTP_REFERER'])) ? '<a href="'. $data['server']['HTTP_REFERER'] .'">'. $data['server']['HTTP_REFERER'] .'</a>' : '';
	}
}

?>