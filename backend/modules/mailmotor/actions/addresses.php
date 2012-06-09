<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This page will display the overview of addresses
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorAddresses extends BackendBaseActionIndex
{
	const PAGING_LIMIT = 10;

	/**
	 * Filter variables
	 *
	 * @var	array
	 */
	private $filter;

	/**
	 * The passed group record
	 *
	 * @var	array
	 */
	private $group;

	/**
	 * Builds the query for this datagrid
	 *
	 * @return array		An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		/*
		 * Start query, as you can see this query is built in the wrong place, because of the filter
		 * it is a special case where we allow the query to be in the actionfile itself
		 */
		$query =
			'SELECT ma.email, ma.source, UNIX_TIMESTAMP(ma.created_on) AS created_on
			 FROM mailmotor_addresses AS ma
			 LEFT OUTER JOIN mailmotor_addresses_groups AS mag ON mag.email = ma.email
			 WHERE 1';

		// init parameters
		$parameters = array();

		// add name
		if($this->filter['email'] !== null)
		{
			$query .= ' AND ma.email REGEXP ?';
			$parameters[] = $this->filter['email'];
		}

		// group was set
		if(!empty($this->group))
		{
			$query .= ' AND mag.group_id = ? AND mag.status = ?';
			$parameters[] = $this->group['id'];
			$parameters[] = 'subscribed';
		}

		$query .= ' GROUP BY email';

		return array($query, $parameters);
	}

	/**
	 * Sets the headers so we may download the CSV file in question
	 *
	 * @param string $path The full path to the CSV file you wish to download.
	 * @return array
	 */
	private function downloadCSV($path)
	{
		// check if the file exists
		if(!SpoonFile::exists($path)) throw new SpoonFileException('The file ' . $path . ' doesn\'t exist.');

		// fetch the filename from the path string
		$explodedFilename = explode('/', $path);
		$filename = end($explodedFilename);

		// set headers for download
		$headers[] = 'Content-type: application/csv; charset=' . SPOON_CHARSET;
		$headers[] = 'Content-Disposition: attachment; filename="' . $filename . '"';
		$headers[] = 'Pragma: no-cache';

		// overwrite the headers
		SpoonHTTP::setHeaders($headers);

		// get the file contents
		$content = SpoonFile::getContent($path);

		// output the file contents
		echo $content;
		exit;
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->setGroup();
		$this->setFilter();
		$this->loadDataGrid();
		$this->loadForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the datagrid with the e-mail addresses
	 */
	private function loadDataGrid()
	{
		// fetch query and parameters
		list($query, $parameters) = $this->buildQuery();

		// create datagrid
		$this->dataGrid = new BackendDataGridDB($query, $parameters);

		// overrule default URL
		$this->dataGrid->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]', 'email' => $this->filter['email']), false));

		// add the group to the URL if one is set
		if(!empty($this->group)) $this->dataGrid->setURL('&group_id=' . $this->group['id'], true);

		// set headers values
		$headers['created_on'] = SpoonFilter::ucfirst(BL::lbl('Created'));

		// set headers
		$this->dataGrid->setHeaderLabels($headers);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('email', 'source', 'created_on'), 'email');

		// add the multicheckbox column
		$this->dataGrid->addColumn('checkbox', '<span class="checkboxHolder block"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="emails[]" value="[email]" class="inputCheckbox" /></span>');
		$this->dataGrid->setColumnsSequence('checkbox');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('export' => BL::lbl('Export'), 'delete' => BL::lbl('Delete')), 'delete');
		$this->dataGrid->setMassAction($ddmMassAction);

		// set column functions
		$this->dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

		// add edit column
		$editURL = BackendModel::createURLForAction('edit_address') . '&amp;email=[email]';
		if(!empty($this->group)) $editURL .= '&amp;group_id=' . $this->group['id'];
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), $editURL, BL::lbl('Edit'));

		// set paging limit
		$this->dataGrid->setPagingLimit(self::PAGING_LIMIT);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('filter', null, 'get');

		// add fields
		$this->frm->addText('email', $this->filter['email']);
		$this->frm->addHidden('group_id', $this->group['id']);

		// manually parse fields
		$this->frm->parse($this->tpl);

		// check if the filter form was set
		if($this->frm->isSubmitted()) $this->tpl->assign('oPost', true);
	}

	/**
	 * Parse all datagrids
	 */
	protected function parse()
	{
		parent::parse();

		// CSV parameter (this is set when an import partially fails)
		$csv = $this->getParameter('csv');
		$download = $this->getParameter('download', 'bool', false);

		// a failed import just happened
		if(!empty($csv))
		{
			// assign the CSV URL to the template
			$this->tpl->assign('csvURL', BackendModel::createURLForAction('addresses') . '&csv=' . $csv . '&download=1');

			// we should download the file
			if($download)
			{
				$this->downloadCSV(BACKEND_CACHE_PATH . '/mailmotor/' . $csv);
			}
		}

		// parse the datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// parse paging & sorting
		$this->tpl->assign('offset', (int) $this->dataGrid->getOffset());
		$this->tpl->assign('order', (string) $this->dataGrid->getOrder());
		$this->tpl->assign('sort', (string) $this->dataGrid->getSort());

		// parse filter
		$this->tpl->assign($this->filter);
	}

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
		// set filter values
		$this->filter['email'] = $this->getParameter('email');
	}

	/**
	 * Sets the group record
	 */
	private function setGroup()
	{
		// set the passed group ID
		$id = SpoonFilter::getGetValue('group_id', null, 0, 'int');

		// group was set
		if(!empty($id))
		{
			// get group record
			$this->group = BackendMailmotorModel::getGroup($id);

			// assign the group record
			$this->tpl->assign('group', $this->group);
		}
	}
}
