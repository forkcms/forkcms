<?php

/**
 * This page will display the overview of custom fields
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorCustomFields extends BackendBaseActionIndex
{
	// maximum number of items
	const PAGING_LIMIT = 10;


	/**
	 * The group record
	 *
	 * @var	array
	 */
	private $group;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get data
		$this->getData();

		// load datagrid
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Gets data related to custom fields
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get passed group ID
		$id = SpoonFilter::getGetValue('group_id', null, 0, 'int');

		// fetch group record
		$this->group = BackendMailmotorModel::getGroup($id);

		// group doesn't exist
		if(empty($this->group)) $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');

		// no custom fields for this group
		if(empty($this->group['custom_fields'])) $this->group['custom_fields'] = array();

		// loop the record's custom fields
		foreach($this->group['custom_fields'] as $key => $field)
		{
			// reformat the custom fields so they work in a datagrid
			$this->group['custom_fields'][$key] = array('name' => $field);
		}
	}


	/**
	 * Loads the datagrid with the campaigns
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridArray($this->group['custom_fields']);

		// set headers values
		$headers['name'] = ucfirst(BL::lbl('Title'));

		// set headers
		$this->dataGrid->setHeaderLabels($headers);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name'), 'name');
		$this->dataGrid->setSortParameter('asc');

		// add the multicheckbox column
		$this->dataGrid->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="fields[]" value="[name]" class="inputCheckbox" /></div>');
		$this->dataGrid->setColumnsSequence('checkbox');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
		$this->dataGrid->setMassAction($ddmMassAction);

		// add styles
		$this->dataGrid->setColumnAttributes('name', array('class' => 'title'));

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

		// parse group record in template
		$this->tpl->assign('group', $this->group);
	}


	/**
	 * Sets a link to the campaign statistics if it contains sent mailings
	 *
	 * @return	string
	 * @param	int $id		The ID of the campaign.
	 */
	public function setStatisticsLink($id)
	{
		// build the link HTML
		$html = '<a href="' . BackendModel::createURLForAction('statistics_campaign') . '&id=' . $id . '" class="button icon iconStats linkButton"><span><span><span>' . BL::lbl('Statistics') . '</span></span></span></a>';

		// check if this campaign has sent mailings
		$hasSentMailings = (BackendMailmotorModel::existsSentMailingsByCampaignID($id) > 0) ? true : false;

		// return the result
		return ($hasSentMailings) ? $html : '';
	}
}

?>