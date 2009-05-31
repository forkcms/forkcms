<?php

/**
 * SpotlightIndex
 *
 * This is the index-action (default), it will display the spotlight-overview
 *
 * @package		backend
 * @subpackage	spotlight
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class SpotlightIndex extends BackendBaseActionIndex
{
	/**
	 * Datagrid instance
	 *
	 * @var	BackendDataGridDB
	 */
	private $datagrid;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the datagrid
		$this->loadDatagrid();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendSpotlightModel::QRY_BROWSE, array('active'));

		// hide columns
		$this->datagrid->setColumnsHidden(array('id'));

		// set headers
		$this->datagrid->setHeaderLabels(array('title' => BL::getLabel('Title'), 'sequence' => '&nbsp;'));

		// enable drag-and-drop
		$this->datagrid->enableSequenceByDragAndDrop();

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'?id=[id]', BL::getLabel('Edit'));

		// set id on rows, we will need this for the hilighting
		$this->datagrid->setRowAttributes(array('id' => 'id-[id]'));
	}


	/**
	 * Parse the datagrid and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>