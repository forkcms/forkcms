<?php

/**
 * This is the statistics-action, it will display the overview of search statistics
 *
 * @package		backend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
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
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendSearchModel::QRY_DATAGRID_BROWSE_STATISTICS, BL::getWorkingLanguage());

		// hide column
		$this->dataGrid->setColumnsHidden('data');

		// create column
		$this->dataGrid->addColumn('ip', BL::lbl('IP'));
		$this->dataGrid->addColumn('referrer', BL::lbl('Referrer'));

		// header labels
		$this->dataGrid->setHeaderLabels(array('time' => ucfirst(BL::lbl('SearchedOn'))));

		// set column function
		$this->dataGrid->setColumnFunction(array(__CLASS__, 'setIp'), '[data]', 'ip');
		$this->dataGrid->setColumnFunction(array(__CLASS__, 'setReferrer'), '[data]', 'referrer');
		$this->dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[time]'), 'time', true);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('time', 'term'), 'time');
		$this->dataGrid->setSortParameter('desc');
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign the datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
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
		return (isset($data['server']['HTTP_REFERER'])) ? '<a href="' . $data['server']['HTTP_REFERER'] . '">' . $data['server']['HTTP_REFERER'] . '</a>' : '';
	}
}

?>