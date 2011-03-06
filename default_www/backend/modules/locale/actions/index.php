<?php

/**
 * This is the index-action, it will display the overview of locale items.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendLocaleIndex extends BackendBaseActionIndex
{
	/**
	 * Filter variables
	 *
	 * @var	array
	 */
	private $filter;


	/**
	 * Form
	 *
	 * @var BackendForm
	 */
	private $frm;


	/**
	 * Builds the query for this datagrid
	 *
	 * @return	array		An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		// init var
		$parameters = array();

		// start query, as you can see this query is build in the wrong place, because of the filter
		// it is a special case wherein we allow the query to be in the actionfile itself
		$query = 'SELECT l.id, l.language, l.application, l.module, l.type, l.name, l.value
					FROM locale AS l
					WHERE 1';

		// add language
		if($this->filter['language'] !== null)
		{
			$query .= ' AND l.language = ?';
			$parameters[] = $this->filter['language'];
		}

		// add application
		if($this->filter['application'] !== null)
		{
			$query .= ' AND l.application = ?';
			$parameters[] = $this->filter['application'];
		}

		// add module
		if($this->filter['module'] !== null)
		{
			$query .= ' AND l.module = ?';
			$parameters[] = $this->filter['module'];
		}

		// add type
		if($this->filter['type'] !== null)
		{
			$query .= ' AND l.type = ?';
			$parameters[] = $this->filter['type'];
		}

		// add name
		if($this->filter['name'] !== null)
		{
			$query .= ' AND l.name LIKE ?';
			$parameters[] = '%' . $this->filter['name'] . '%';
		}

		// add value
		if($this->filter['value'] !== null)
		{
			$query .= ' AND l.value LIKE ?';
			$parameters[] = '%' . $this->filter['value'] . '%';
		}

		return array($query, $parameters);
	}


	/**
	 * Execute the action
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
	 * Get the name of the languages
	 *
	 * @return	string
	 * @param	string $language	The language to get.
	 */
	public static function getLanguage($language)
	{
		return BL::msg(mb_strtoupper((string) $language), 'core');
	}


	/**
	 * Get a label
	 *
	 * @return	string
	 * @param	string $type		The type to get a label for.
	 */
	public static function getType($type)
	{
		return BL::msg(mb_strtoupper((string) $type), 'core');
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
		$this->datagrid = new BackendDataGridDB($query, $parameters);

		// overrule default URL
		$this->datagrid->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]', 'language' => $this->filter['language'], 'application' => $this->filter['application'], 'module' => $this->filter['module'], 'type' => $this->filter['type'], 'name' => $this->filter['name'], 'value' => $this->filter['value']), false));

		// sorting columns
		$this->datagrid->setSortingColumns(array('language', 'application', 'module', 'type', 'name', 'value'), 'name');

		// set colum URLs
		$this->datagrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// column titles
		$this->datagrid->setHeaderLabels(array('name' => ucfirst(BL::lbl('ReferenceCode')), 'value' => ucfirst(BL::lbl('Translation'))));

		// add the multicheckbox column
		$this->datagrid->setMassActionCheckboxes('checkbox', '[id]');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
		$this->datagrid->setMassAction($ddmMassAction);

		// update value
		$this->datagrid->setColumnFunction(array('BackendDataGridFunctions', 'truncate'), array('[value]', 30), 'value', true);
		$this->datagrid->setColumnFunction(array(__CLASS__, 'getLanguage'), array('[language]'), 'language', true);
		$this->datagrid->setColumnFunction(array(__CLASS__, 'getType'), array('[type]'), 'type', true);

		// add columns
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit', null, null, array('language' => $this->filter['language'], 'application' => $this->filter['application'], 'module' => $this->filter['module'], 'type' => $this->filter['type'], 'name' => $this->filter['name'], 'value' => $this->filter['value'])) . '&amp;id=[id]', BL::lbl('Edit'));
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');

		// add fields
		$this->frm->addText('name', $this->filter['name']);
		$this->frm->addText('value', $this->filter['value']);
		$this->frm->addDropdown('language', BL::getLocaleLanguages(), $this->filter['language']);
		$this->frm->getField('language')->setDefaultElement(ucfirst(BL::lbl('ChooseALanguage')));
		$this->frm->addDropdown('application', array('backend' => 'Backend', 'frontend' => 'Frontend'), $this->filter['application']);
		$this->frm->getField('application')->setDefaultElement(ucfirst(BL::lbl('ChooseAnApplication')));
		$this->frm->addDropdown('module', BackendModel::getModulesForDropDown(false), $this->filter['module']);
		$this->frm->getField('module')->setDefaultElement(ucfirst(BL::lbl('ChooseAModule')));
		$this->frm->addDropdown('type', BackendLocaleModel::getTypesForDropDown(), $this->filter['type']);
		$this->frm->getField('type')->setDefaultElement(ucfirst(BL::lbl('ChooseAType')));

		// manually parse fields
		$this->frm->parse($this->tpl);
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse datagrid
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);

		// parse paging & sorting
		$this->tpl->assign('offset', (int) $this->datagrid->getOffset());
		$this->tpl->assign('order', (string) $this->datagrid->getOrder());
		$this->tpl->assign('sort', (string) $this->datagrid->getSort());

		$this->tpl->assign('addUrl', BackendModel::createURLForAction('add', null, null, $this->filter));

		// is filtered?
		if($this->getParameter('form', 'string', '') == 'filter') $this->tpl->assign('filter', true);

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
		$this->filter['language'] = (isset($_GET['language'])) ? $this->getParameter('language') : BL::getWorkingLanguage();
		$this->filter['application'] = $this->getParameter('application');
		$this->filter['module'] = $this->getParameter('module');
		$this->filter['type'] = $this->getParameter('type');
		$this->filter['name'] = $this->getParameter('name');
		$this->filter['value'] = $this->getParameter('value');
	}
}

?>