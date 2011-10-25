<?php

/**
 * This is the groups-action, it will display the overview of profile groups.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.0
 */
class BackendProfilesGroups extends BackendBaseActionIndex
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

		// start query, as you can see this query is build in the wrong place, because of the filter it is a special case
		// wherin we allow the query to be in the actionfile itself
		$query = 'SELECT pg.id, pg.name, COUNT(gr.id) AS members_count
					FROM profiles_groups AS pg
					LEFT OUTER JOIN profiles_groups_rights AS gr ON gr.group_id = pg.id AND (gr.expires_on IS NULL OR gr.expires_on > NOW())
					GROUP BY pg.id
					HAVING 1';

		// add name
		if($this->filter['name'] !== null)
		{
			$query .= ' AND pg.name LIKE ?';
			$parameters[] = '%' . $this->filter['name'] . '%';
		}

		// query
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
	 * Load the datagrid.
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// fetch query and parameters
		list($query, $parameters) = $this->buildQuery();

		// create datagrid
		$this->dgGroups = new BackendDataGridDB($query, $parameters);

		// overrule default URL
		$this->dgGroups->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]', 'name' => $this->filter['name']), false));

		// sorting columns
		$this->dgGroups->setSortingColumns(array('name', 'members_count'), 'name');

		// set the amount of profiles
		$this->dgGroups->setColumnFunction(array(__CLASS__, 'parseNumProfiles'), array('[id]', '[members_count]'), 'members_count');

		// set column URLs
		$this->dgGroups->setColumnURL('name', BackendModel::createURLForAction('edit_group') . '&amp;id=[id]');
		$this->dgGroups->setColumnURL('members_count', BackendModel::createURLForAction('index') . '&amp;group=[id]');

		// add columns
		$this->dgGroups->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_group') . '&amp;id=[id]');
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

		// add fields
		$this->frm->addText('name', $this->filter['name']);

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
		$this->tpl->assign('dgGroups', ($this->dgGroups->getNumResults() != 0) ? $this->dgGroups->getContent() : false);

		// parse paging & sorting
		$this->tpl->assign('offset', (int) $this->dgGroups->getOffset());
		$this->tpl->assign('order', (string) $this->dgGroups->getOrder());
		$this->tpl->assign('sort', (string) $this->dgGroups->getSort());

		// parse filter
		$this->tpl->assign($this->filter);
	}


	/**
	 * Parse amount of profiles for the datagrid.
	 *
	 * @return	string
	 * @param	int $groupId		Group id.
	 * @param	int $numProfiles	Number of profiles.
	 */
	public static function parseNumProfiles($groupId, $numProfiles)
	{
		// 1 item
		if($numProfiles == 1) $output = '1 ' . BL::getLabel('Profile');

		// no items
		else $output = $numProfiles . ' ' . BL::getLabel('Profiles');

		// complete output
		return '<a href="' . BackendModel::createURLForAction('index') . '&amp;group=' . $groupId . '" title="' . $output . '">' . $output . '</a>';
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		$this->filter['name'] = $this->getParameter('name');
	}
}

?>
