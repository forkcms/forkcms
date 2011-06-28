<?php

/**
 * This page will display the statistical overview of bounces for a specified mailing
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorStatisticsBounces extends BackendBaseActionIndex
{
	// maximum number of items
	const PAGING_LIMIT = 10;


	/**
	 * The list with bounces
	 *
	 * @var	array
	 */
	private $bounces;


	/**
	 * The given mailing record
	 *
	 * @var	array
	 */
	private $mailing;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get the data
		$this->getData();

		// load datagrid
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Gets all data needed for this page
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get parameters
		$id = $this->getParameter('mailing_id', 'int');

		// does the item exist
		if(!BackendMailmotorModel::existsMailing($id)) $this->redirect(BackendModel::createURLForAction('index') . '&error=mailing-does-not-exist');

		// fetch the mailing
		$this->mailing = BackendMailmotorModel::getMailing($id);

		// fetch the bounces
		$this->bounces = BackendMailmotorCMHelper::getBounces($this->mailing['id']);

		// does the item exist
		if(empty($this->bounces)) $this->redirect(BackendModel::createURLForAction('statistics') . '&id=' . $this->mailing['id'] . '&error=no-bounces');
	}


	/**
	 * Loads the datagrid with the clicked link
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create a new source-object
		$source = new SpoonDataGridSourceArray($this->bounces);

		// call the parent, as in create a new datagrid with the created source
		$this->dataGrid = new BackendDataGrid($source);

		// hide the following columns
		$this->dataGrid->setColumnHidden('list_id');

		// sorting columns
		$this->dataGrid->setSortingColumns(array('email', 'bounce_type'), 'email');

		// set paging limit
		$this->dataGrid->setPagingLimit(self::PAGING_LIMIT);
	}


	/**
	 * Parse all datagrids
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// parse mailing record
		$this->tpl->assign('mailing', $this->mailing);
	}
}

?>