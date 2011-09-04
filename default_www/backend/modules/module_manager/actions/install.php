<?php

/**
 * BackendModulemanagerInstall
 * This is the install-action
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerInstall extends BackendBaseActionEdit
{
	/**
	 * The module we want to install
	 *
	 */
	protected $module;
	
	/**
	 * Database connection, needed for installation
	 *
	 * @var	SpoonDatabases
	 */
	private $db;
	
								
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();
		
		$this->db = BackendModel::getDB(true);

		$this->installModule();
	}


	/**
	 * Install the module
	 *
	 * @return	void
	 */
	private function installModule()
	{
		// validate incoming parameters
		if($this->getParameter('module') === null) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
		
		$this->module = $this->getParameter('module');
				
		// Installer class 
		require_once PATH_WWW . '/backend/core/installer/install.php';
		
		// Load installer of module
		if(SpoonFile::exists(PATH_WWW . '/backend/modules/' . $this->module . '/installer/install.php'))
		{
			// Delete local records from db and cache file
			BackendModulemanagerModel::delete($this->module);
			
			// init var
			$variables = array();

			// load file
			require_once PATH_WWW . '/backend/modules/' . $this->module . '/installer/install.php';

			// class name
			$class = SpoonFilter::toCamelCase($this->module) . 'Install';

			// execute installer
			$install = new $class($this->db, BackendLanguage::getActiveLanguages(),BackendLanguage::getActiveLanguages(), false, $variables);
			
			// Rebuild backend navigation cache
			$navigation = new BackendNavigation();
			$navigation->buildCache();
			
			// Rebuild local after install
			BackendModulemanagerModel::buildLocale();
			
			// redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=installed');
			
		}
		else
		{
			$this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing-installer');
		}
	}


}

?>