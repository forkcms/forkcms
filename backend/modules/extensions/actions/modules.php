<?php

/**
 * This is the modules-action, it will display the overview of modules.
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		3.0.0
 */
class BackendExtensionsModules extends BackendBaseActionIndex
{
	/**
	 * Data grids.
	 *
	 * @var	BackendDataGrid
	 */
	private $dataGridInstalledModules, $dataGridInstallableModules;


	/**
	 * Modules that are or or not installed.
	 * This is used as a source for the data grids.
	 *
	 * @var	array
	 */
	private $installedModules = array(), $installableModules = array();


	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the data for the data grids
		$this->loadData();

		// load the data grids
		$this->loadDataGridInstalled();
		$this->loadDataGridInstallable();

		// parse the data grid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the data for the 2 data grids.
	 *
	 * @return	void
	 */
	private function loadData()
	{
		// get all managable modules
		$modules = BackendExtensionsModel::getModules();

		// split the modules in 2 seperate data grid sources
		foreach($modules as $module)
		{
			if($module['installed']) $this->installedModules[] = $module;
			else $this->installableModules[] = $module;
		}
	}


	/**
	 * Load the data grid for installable modules.
	 *
	 * @return	void
	 */
	private function loadDataGridInstallable()
	{
		// create datagrid
		$this->dataGridInstallableModules = new BackendDataGridArray($this->installableModules);

		// sorting columns
		$this->dataGridInstallableModules->setSortingColumns(array('raw_name'));

		// set header labels
		$this->dataGridInstallableModules->setHeaderLabels(array('raw_name' => ucfirst(BL::getLabel('Name'))));

		// hide some columns
		$this->dataGridInstallableModules->setColumnsHidden(array('installed', 'name'));

		// set colum URLs
		$this->dataGridInstallableModules->setColumnURL('raw_name', BackendModel::createURLForAction('module_detail') . '&amp;module=[raw_name]');

		// add details column
		$this->dataGridInstallableModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('module_detail') . '&amp;module=[raw_name]', BL::lbl('Details'));

		// add install column
		$this->dataGridInstallableModules->addColumn('install', null, BL::lbl('Install'), BackendModel::createURLForAction('module_install') . '&amp;module=[raw_name]', BL::lbl('Install'));
		$this->dataGridInstallableModules->setColumnConfirm('install', sprintf(BL::msg('ConfirmModuleInstall'), '[raw_name]'));
	}


	/**
	 * Load the data grid for installed modules.
	 *
	 * @return	void
	 */
	private function loadDataGridInstalled()
	{
		// create datagrid
		$this->dataGridInstalledModules = new BackendDataGridArray($this->installedModules);

		// sorting columns
		$this->dataGridInstalledModules->setSortingColumns(array('name'));

		// hide some columns
		$this->dataGridInstalledModules->setColumnsHidden(array('installed', 'raw_name'));

		// set colum URLs
		$this->dataGridInstalledModules->setColumnURL('name', BackendModel::createURLForAction('module_detail') . '&amp;module=[raw_name]');

		// add details column
		$this->dataGridInstalledModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('module_detail') . '&amp;module=[raw_name]', BL::lbl('Details'));
	}


	/**
	 * Parse the datagrids and the reports.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse data grid
		$this->tpl->assign('dataGridInstallableModules', (string) $this->dataGridInstallableModules->getContent());
		$this->tpl->assign('dataGridInstalledModules', (string) $this->dataGridInstalledModules->getContent());
	}
}

?>