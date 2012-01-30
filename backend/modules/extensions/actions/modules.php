<?php

/**
 * This is the modules-action, it will display the overview of modules.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendExtensionsModules extends BackendBaseActionIndex
{
	/**
	 * Data grids.
	 *
	 * @var BackendDataGrid
	 */
	private $dataGridInstalledModules, $dataGridInstallableModules;

	/**
	 * Modules that are or or not installed.
	 * This is used as a source for the data grids.
	 *
	 * @var array
	 */
	private $installedModules = array(), $installableModules = array();

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();
		$this->loadData();
		$this->loadDataGridInstalled();
		$this->loadDataGridInstallable();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the data for the 2 data grids.
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
	 */
	private function loadDataGridInstallable()
	{
		// create datagrid
		$this->dataGridInstallableModules = new BackendDataGridArray($this->installableModules);

		// sorting columns
		$this->dataGridInstallableModules->setSortingColumns(array('raw_name'));

		// set header labels
		$this->dataGridInstallableModules->setHeaderLabels(array('raw_name' => SpoonFilter::ucfirst(BL::getLabel('Name'))));

		// hide some columns
		$this->dataGridInstallableModules->setColumnsHidden(array('installed', 'name', 'cronjobs_active'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('detail_module'))
		{
			// set colum URLs
			$this->dataGridInstallableModules->setColumnURL('raw_name', BackendModel::createURLForAction('detail_module') . '&amp;module=[raw_name]');

			// add details column
			$this->dataGridInstallableModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('detail_module') . '&amp;module=[raw_name]', BL::lbl('Details'));
		}

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('install_module'))
		{
			// add install column
			$this->dataGridInstallableModules->addColumn('install', null, BL::lbl('Install'), BackendModel::createURLForAction('install_module') . '&amp;module=[raw_name]', BL::lbl('Install'));
			$this->dataGridInstallableModules->setColumnConfirm('install', sprintf(BL::msg('ConfirmModuleInstall'), '[raw_name]'));
		}
	}

	/**
	 * Load the data grid for installed modules.
	 */
	private function loadDataGridInstalled()
	{
		// create datagrid
		$this->dataGridInstalledModules = new BackendDataGridArray($this->installedModules);

		// sorting columns
		$this->dataGridInstalledModules->setSortingColumns(array('name'));

		// hide some columns
		$this->dataGridInstalledModules->setColumnsHidden(array('installed', 'raw_name', 'cronjobs_active'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('detail_module'))
		{
			// set colum URLs
			$this->dataGridInstalledModules->setColumnURL('name', BackendModel::createURLForAction('detail_module') . '&amp;module=[raw_name]');

			// add details column
			$this->dataGridInstalledModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('detail_module') . '&amp;module=[raw_name]', BL::lbl('Details'));
		}
	}

	/**
	 * Parse the datagrids and the reports.
	 */
	protected function parse()
	{
		parent::parse();

		// parse data grid
		$this->tpl->assign('dataGridInstallableModules', (string) $this->dataGridInstallableModules->getContent());
		$this->tpl->assign('dataGridInstalledModules', (string) $this->dataGridInstalledModules->getContent());

		// parse installer warnings
		$this->tpl->assign('warnings', (array) SpoonSession::get('installer_warnings'));
	}
}
