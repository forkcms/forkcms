<?php

/**
 * This is the module install-action.
 * It will install the module given via the "module" GET parameter.
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		3.0.0
 */
class BackendExtensionsModuleInstall extends BackendBaseActionIndex
{
	/**
	 * Module we request the details of.
	 *
	 * @var	string
	 */
	private $currentModule;


	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->currentModule = $this->getParameter('module', 'string');

		// does the item exist
		if($this->currentModule !== null && BackendExtensionsModel::existsModule($this->currentModule))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// make sure this module can be installed
			$this->validateInstall();

			// do the actual install
			BackendExtensionsModel::installModule($this->currentModule);

			// redirect to index with a success message
			$this->redirect(BackendModel::createURLForAction('modules') . '&report=module-installed&var=' . $this->currentModule . '&highlight=row-module_' . $this->currentModule);
		}

		// no item found, redirect to index, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('modules') . '&error=non-existing');
	}


	/**
	 * Validate if the module can be installed.
	 *
	 * @return	void
	 */
	private function validateInstall()
	{
		// already installed
		if(BackendExtensionsModel::isModuleInstalled($this->currentModule))
		{
			$this->redirect(BackendModel::createURLForAction('modules') . '&error=already-installed&var=' . $this->currentModule);
		}

		// no installer class present
		if(!SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $this->currentModule . '/installer/installer.php'))
		{
			$this->redirect(BackendModel::createURLForAction('modules') . '&error=no-installer-file&var=' . $this->currentModule);
		}
	}
}

?>