<?php

/**
 * This page will display the statistical overview of who clicked a certain link in a specified mailing
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorStatisticsLink extends BackendBaseActionIndex
{
	// maximum number of items
	const PAGING_LIMIT = 10;


	/**
	 * The given mailing record
	 *
	 * @var	array
	 */
	private $mailing;


	/**
	 * The statistics record
	 *
	 * @var	array
	 */
	private $statistics;


	/**
	 * The given link URL
	 *
	 * @var	string
	 */
	public $linkURL;


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

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

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
		$this->linkURL = $this->getParameter('url');

		// does the item exist
		if(!BackendMailmotorModel::existsMailing($id)) $this->redirect(BackendModel::createURLForAction('index') . '&error=mailing-does-not-exist');
		if($this->linkURL == '') $this->redirect(BackendModel::createURLForAction('statistics') . '&id=' . $id . '&error=link-does-not-exist');

		// fetch the statistics
		$this->statistics = BackendMailmotorCMHelper::getStatistics($id, true);

		// fetch the mailing
		$this->mailing = BackendMailmotorModel::getMailing($id);

		// no stats found
		if($this->statistics === false) $this->redirect(BackendModel::createURLForAction('index') . '&error=no-statistics-loaded');
	}


	/**
	 * Loads the datagrid with the clicked link
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// no statistics found
		if(empty($this->statistics['clicked_links_by'][$this->linkURL])) return false;

		// create a new source-object
		$source = new SpoonDataGridSourceArray($this->statistics['clicked_links_by'][$this->linkURL]);

		// call the parent, as in create a new datagrid with the created source
		$this->dataGrid = new BackendDataGrid($source);
		$this->dataGrid->setColumnsHidden(array('list_id', 'url'));

		// set header labels
		$this->dataGrid->setHeaderLabels(array('ip' => BL::lbl('IpAddress')));

		// sorting columns
		$this->dataGrid->setSortingColumns(array('email'), 'email');

		// set paging limit
		$this->dataGrid->setPagingLimit(self::PAGING_LIMIT);
	}


	/**
	 * Load the form for the group
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// add fields
		$this->frm->addText('group');
	}


	/**
	 * Parse all datagrids
	 *
	 * @return	void
	 */
	private function parse()
	{
		// manually parse fields
		$this->frm->parse($this->tpl);

		// parse the datagrid
		if(!empty($this->statistics['clicked_links_by'][$this->linkURL])) $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// parse the mailing ID and url
		$this->tpl->assign('url', $this->linkURL);

		// parse statistics
		$this->tpl->assign('stats', $this->statistics);

		// parse mailing record
		$this->tpl->assign('mailing', $this->mailing);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// shorten fields
			$txtGroup = $this->frm->getField('group');

			// validate fields
			if($txtGroup->isFilled(BL::err('NameIsRequired')))
			{
				if(BackendMailmotorModel::existsGroupByName($txtGroup->getValue())) $txtGroup->addError(BL::err('GroupAlreadyExists'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['name'] = $txtGroup->getValue();
				$item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

				// update the item
				$item['id'] = BackendMailmotorCMHelper::insertGroup($item);

				// loop the adresses
				foreach($this->statistics['clicked_links_by'][$this->linkURL] as $clicker)
				{
					// subscribe the user to the created group
					BackendMailmotorCMHelper::subscribe($clicker['email'], $item['id']);
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('statistics_link') . '&url=' . $this->linkURL . '&mailing_id=' . $this->mailing['id'] . '&report=group-added&var=' . urlencode($item['name']) . '&highlight=id-' . $this->mailing['id']);
			}
		}
	}
}

?>