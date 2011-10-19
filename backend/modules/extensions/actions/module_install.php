<?php

/**
 * This is the install-action it will install a module.
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
	 * Clear all applications cache.
	 *
	 * Note: we do not need to rebuild anything, the core will do this when noticing the cache files are missing.
	 *
	 * @return	void
	 */
	private function clearCache()
	{
		// list of cache files to be deleted
		$filesToDelete = array();

		// backend navigation
		$filesToDelete[] = BACKEND_CACHE_PATH . '/navigation/navigation.php';

		// backend locale
		foreach(SpoonFile::getList(BACKEND_CACHE_PATH . '/locale', '/\.php$/') as $file)
		{
			$filesToDelete[] = BACKEND_CACHE_PATH . '/locale/' . $file;
		}

		// frontend navigation
		foreach(SpoonFile::getList(FRONTEND_CACHE_PATH . '/navigation', '/\.(php|js)$/') as $file)
		{
			$filesToDelete[] = FRONTEND_CACHE_PATH . '/navigation/' . $file;
		}

		// frontend locale
		foreach(SpoonFile::getList(FRONTEND_CACHE_PATH . '/locale', '/\.php$/') as $file)
		{
			$filesToDelete[] = FRONTEND_CACHE_PATH . '/locale/' . $file;
		}

		// delete the files
		foreach($filesToDelete as $file) SpoonFile::delete($file);
	}


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
			$this->install();

			// redirect to index with a success message
			$this->redirect(BackendModel::createURLForAction('modules') . '&report=module-installed&var=' . $this->currentModule . '&highlight=row-module_' . $this->currentModule);
		}

		// no item found, redirect to index, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('modules') . '&error=non-existing');
	}


	/**
	 * Execute the modules installer
	 *
	 * @return	void
	 */
	private function install()
	{
		// we need the installer
		require_once BACKEND_CORE_PATH . '/installer/installer.php';
		require_once BACKEND_MODULES_PATH . '/' . $this->currentModule . '/installer/installer.php';

		// installer class name
		$class = SpoonFilter::toCamelCase($this->currentModule) . 'Installer';

		// possible variables available for the module installers
		$variables = array();

		// init installer
		$installer = new $class(
			BackendModel::getDB(true),
			BL::getActiveLanguages(),
			array_keys(BL::getInterfaceLanguages()),
			false,
			$variables
		);

		// execute installation
		$installer->install();

		// clear all cache
		$this->clearCache();
	}


	/**
	 * Validate if the module can be installed.
	 *
	 * @return	void
	 */
	private function validateInstall()
	{
		// already installed
		if(BackendExtensionsModel::isInstalled($this->currentModule))
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