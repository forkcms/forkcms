<?php

/**
 * This is the index-action, it will display the overview of profiles.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.0
 */
class BackendProfilesIndex extends BackendBaseActionIndex
{
	/**
	 * Filter variables.
	 *
	 * @var	array
	 */
	private $filter;


	/**
	 * Form.
	 *
	 * @var BackendForm
	 */
	private $frm;


	/**
	 * Builds the query for this datagrid.
	 *
	 * @return	array		An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		// init var
		$parameters = array();

		// construct the query in the controller instead of the model as an allowed exception for data grid usage
		$query = 'SELECT p.id, p.email, p.display_name, p.status, UNIX_TIMESTAMP(p.registered_on) AS registered_on FROM profiles AS p';
		$where = array();

		// add status
		if(isset($this->filter['status']))
		{
			$where[] = 'p.status = ?';
			$parameters[] = $this->filter['status'];
		}

		// add email
		if(isset($this->filter['email']))
		{
			$where[] = 'p.email LIKE ?';
			$parameters[] = '%' . $this->filter['email'] . '%';
		}

		// add group
		if(isset($this->filter['group']))
		{
			$query .= ' INNER JOIN profiles_groups_rights AS pgr ON pgr.profile_id = p.id AND (pgr.expires_on IS NULL OR pgr.expires_on > NOW())';
			$where[] .= 'pgr.group_id = ?';
			$parameters[] = $this->filter['group'];
		}

		// query
		if(!empty($where))
		{
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		// group by profile (might have doubles because of the join on groups_rights)
		$query .= ' GROUP BY p.id';

		// query with matching parameters
		return array($query, $parameters);
	}


	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// use our custom JavaScript
		$this->header->addJS('profiles.js');

		// set filter
		$this->setFilter();

		// load form
		$this->loadForm();

		// load datagrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the datagrid
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// fetch query and parameters
		list($query, $parameters) = $this->buildQuery();

		// create datagrid
		$this->dgProfiles = new BackendDataGridDB($query, $parameters);

		// overrule default URL
		$this->dgProfiles->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]', 'email' => $this->filter['email'], 'status' => $this->filter['status'], 'group' => $this->filter['group']), false));

		// sorting columns
		$this->dgProfiles->setSortingColumns(array('email', 'display_name', 'status', 'registered_on'), 'email');

		// set column URLs
		$this->dgProfiles->setColumnURL('email', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// set column function
		$this->dgProfiles->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[registered_on]'), 'registered_on', true);

		// add columns
		$this->dgProfiles->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit', null, null, null) . '&amp;id=[id]', BL::getLabel('Edit'));

		// add the mass action controls
		$this->dgProfiles->setMassActionCheckboxes('checkbox', '[id]');
		$ddmMassAction = new SpoonFormDropdown('action', array('addToGroup' => BL::getLabel('AddToGroup'), 'delete' => BL::getLabel('Delete')), 'addToGroup');
		$ddmMassAction->setAttribute('id', 'massAction');
		$ddmMassAction->setOptionAttributes('addToGroup', array('data-message-id' => 'confirmAddToGroup'));
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDelete'));
		$this->dgProfiles->setMassAction($ddmMassAction);
	}


	/**
	 * Load the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');

		// values for dropdowns
		$status = BackendProfilesModel::getStatusForDropDown();
		$groups = BackendProfilesModel::getGroups();

		// add fields
		$this->frm->addText('email', $this->filter['email']);
		$this->frm->addDropdown('status', $status, $this->filter['status']);
		$this->frm->getField('status')->setDefaultElement('');

		// add a group filter if wa have groups
		if(!empty($groups))
		{
			$this->frm->addDropdown('group', $groups, $this->filter['group']);
			$this->frm->getField('group')->setDefaultElement('');
		}

		// manually parse fields
		$this->frm->parse($this->tpl);
	}


	/**
	 * Parse & display the page.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse datagrid
		$this->tpl->assign('dgProfiles', ($this->dgProfiles->getNumResults() != 0) ? $this->dgProfiles->getContent() : false);

		// parse paging & sorting
		$this->tpl->assign('offset', (int) $this->dgProfiles->getOffset());
		$this->tpl->assign('order', (string) $this->dgProfiles->getOrder());
		$this->tpl->assign('sort', (string) $this->dgProfiles->getSort());

		// parse filter
		$this->tpl->assign($this->filter);
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		$this->filter['email'] = $this->getParameter('email');
		$this->filter['status'] = $this->getParameter('status');
		$this->filter['group'] = $this->getParameter('group');
	}
}

?>
