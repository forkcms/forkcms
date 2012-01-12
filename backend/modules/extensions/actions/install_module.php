<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the module install-action.
 * It will install the module given via the "module" GET parameter.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendExtensionsInstallModule extends BackendBaseActionIndex
{
	/**
	 * Module we want to install.
	 *
	 * @var string
	 */
	private $currentModule;

	/**
	 * Execute the action.
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
