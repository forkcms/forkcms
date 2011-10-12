<?php

/**
 * This is the templates-action, it will display the templates-overview
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesTemplates extends BackendBaseActionEdit
{
	/**
	 * All available themes
	 *
	 * @var	array
	 */
	private $availableThemes;


	/**
	 * The current selected theme
	 *
	 * @var	string
	 */
	private $selectedTheme;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load record
		$this->loadData();

		// load the form
		$this->loadForm();

		// load the datagrid
		$this->loadDataGrid();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the record
	 *
	 * @return	void
	 */
	private function loadData()
	{
		// get data
		$this->selectedTheme = $this->getParameter('theme', 'string');

		// build available themes
		$this->availableThemes = BackendModel::getThemes();

		// determine selected theme, based upon submitted form or default theme
		$this->selectedTheme = SpoonFilter::getValue($this->selectedTheme, array_keys($this->availableThemes), BackendModel::getModuleSetting('core', 'theme', 'core'));
	}


	/**
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendPagesModel::QRY_BROWSE_TEMPLATES, array($this->selectedTheme));

		// set colum URLs
		$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit_template') . '&amp;id=[id]');

		// add edit column
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_template') . '&amp;id=[id]', BL::lbl('Edit'));
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('themes');

		// create elements
		$this->frm->addDropdown('theme', $this->availableThemes, $this->selectedTheme, false, 'inputDropdown dontCheckBeforeUnload', 'inputDropdownError dontCheckBeforeUnload');
	}


	/**
	 * Parse the datagrid and the reports
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// assign the selected theme, so we can propagate it to the add/edit actions.
		$this->tpl->assign('selectedTheme', urlencode($this->selectedTheme));
	}
}

?>
