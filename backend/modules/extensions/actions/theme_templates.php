<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the templates-action, it will display the templates-overview
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendExtensionsThemeTemplates extends BackendBaseActionEdit
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
	 */
	public function execute()
	{
		parent::execute();
		$this->loadData();
		$this->loadForm();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the record
	 */
	private function loadData()
	{
		// get data
		$this->selectedTheme = $this->getParameter('theme', 'string');

		// build available themes
		foreach(BackendExtensionsModel::getThemes() as $theme) $this->availableThemes[$theme['value']] = $theme['label'];

		// determine selected theme, based upon submitted form or default theme
		$this->selectedTheme = SpoonFilter::getValue($this->selectedTheme, array_keys($this->availableThemes), BackendModel::getModuleSetting('core', 'theme', 'core'));
	}

	/**
	 * Load the datagrids
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendExtensionsModel::QRY_BROWSE_TEMPLATES, array($this->selectedTheme));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit_theme_template'))
		{
			// set colum URLs
			$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit_theme_template') . '&amp;id=[id]');

			// add edit column
			$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_theme_template') . '&amp;id=[id]', BL::lbl('Edit'));
		}
	}

	/**
	 * Load the form
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
	 */
	protected function parse()
	{
		parent::parse();

		// assign datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// assign the selected theme, so we can propagate it to the add/edit actions.
		$this->tpl->assign('selectedTheme', urlencode($this->selectedTheme));
	}
}
