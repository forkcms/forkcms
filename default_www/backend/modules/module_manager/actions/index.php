<?php
/**
 * BackendModulemanagerIndex
 * This is the index-action (default), it will display the modules
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author 		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerIndex extends BackendBaseActionIndex
{

	private $moduleFolders = array();
	private $nonInstalledModules = array();
	private $activeModules = array();
	private $nonActiveModules = array();
	

	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();
		
		$this->loadModules();
		
		$this->compareModules();

		// load the form
		$this->loadDatagrid();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		$this->dgNonInstalled = new BackendDataGridArray($this->nonInstalledModules);
		$this->dgNonInstalled->addColumn('add', null, BL::getLabel('Install'), BackendModel::createURLForAction('install') .'&amp;module=[name]', BL::getLabel('Install'));
		$this->dgNonInstalled->setColumnFunction(create_function('$module,$url,$path_www,$label','return SpoonFile::exists("$path_www/backend/modules/$module/installer/install.php") ? "<a class=\"button icon addIcon linkButton\" href=\"$url&amp;module=$module\"><span>$label</span></a>" : Bl::getLabel("NoInstaller");'),array('[name]',BackendModel::createURLForAction('install'),PATH_WWW,ucfirst(BL::getLabel('Install'))),'add',true);		
		
		
		$this->dgInstalledActive = new BackendDataGridArray($this->activeModules);
		$this->dgInstalledActive->addColumn('missing', ucfirst(BL::getLabel('MissingActions')));
		
		$this->dgInstalledActive->addColumn('actions', null, BL::getLabel('Actions'), BackendModel::createURLForAction('actions') .'&amp;module=[name]', BL::getLabel('Actions'));
		$this->dgInstalledActive->addColumn('reinstall', null, BL::getLabel('Reinstall'), BackendModel::createURLForAction('install') .'&amp;module=[name]', BL::getLabel('Reinstall'));
		$this->dgInstalledActive->addColumn('add', null, BL::getLabel('AddAction'), BackendModel::createURLForAction('add_action') .'&amp;module=[name]', BL::getLabel('AddAction'));
		$this->dgInstalledActive->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&amp;module=[name]', BL::getLabel('Edit'));
		$this->dgInstalledActive->setColumnAttributes('reinstall', array('class' => 'action'));
		$this->dgInstalledActive->setColumnAttributes('actions', array('class' => 'action'));
		
		$this->dgInstalledActive->setColumnFunction(create_function('$module,$url,$path_www,$label','return SpoonFile::exists("$path_www/backend/modules/$module/installer/install.php") ? "<a class=\"button linkButton\" href=\"$url&amp;module=$module\"><span>$label</span></a>" : Bl::getLabel("NoInstaller");'),array('[name]',BackendModel::createURLForAction('install'),PATH_WWW,ucfirst(BL::getLabel('Reinstall'))),'reinstall',true);		
		$this->dgInstalledActive->setColumnFunction(create_function('$module,$url,$label','return"<a class=\"button linkButton\" href=\"$url&amp;module=$module\"><span>$label</span></a>";'),array('[name]',BackendModel::createURLForAction('actions'),ucfirst(BL::getLabel('Actions'))),'actions',true);
		
		$this->dgInstalledActive->setColumnFunction(create_function('$module,$url,$path_www,$label','return SpoonFile::exists("$path_www/backend/modules/$module/installer/install.php") ? "<a class=\"button linkButton\" href=\"$url&amp;module=$module\"><span>$label</span></a>" : Bl::getLabel("NoInstaller");'),array('[name]',BackendModel::createURLForAction('install'),PATH_WWW,ucfirst(BL::getLabel('Reinstall'))),'reinstall',true);		
		$this->dgInstalledActive->setColumnFunction(array(__CLASS__,'hasMissingActions'),array('[name]'),'missing','true');		
		
		
		
		$this->dgInstalledNonActive = new BackendDataGridArray($this->nonActiveModules);
		//$this->dgInstalledNonActive->addColumn('reinstall', null, BL::getLabel('ReInstall'), BackendModel::createURLForAction('install') .'&amp;module=[name]', BL::getLabel('ReInstall'));
		$this->dgInstalledNonActive->addColumn('add', null, BL::getLabel('AddAction'), BackendModel::createURLForAction('add_action') .'&amp;module=[name]', BL::getLabel('AddAction'));
		$this->dgInstalledNonActive->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&amp;module=[name]', BL::getLabel('Edit'));
		
		
		// disable paging
		$this->dgInstalledNonActive->setPaging(false);
		$this->dgInstalledActive->setPaging(false);
		$this->dgNonInstalled->setPaging(false);
	}
	
	
	public static function hasMissingActions($module)
	{
		$missing_actions = BackendModulemanagerModel::getMissingActions($module);
		return empty($missing_actions) ? ucfirst(BL::getlabel('No')) : ucfirst(BL::getlabel('Yes'));
	}
	
	/**
	 * Scans the directory structure for modules and adds them to the list of optional modules
	 *
	 * @return	void
	 */
	private function loadModules()
	{
		// fetch modules
		$exclude = array('core','error','pages','users','locale','authentication','dashboard','settings');
	
		$this->moduleFolders = SpoonDirectory::getList(PATH_WWW .'/backend/modules', false, $exclude, '/^[a-z0-9_]+$/i');
		$this->activeModules = BackendModulemanagerModel::getActiveModules('Y',$exclude);
		$this->nonActiveModules = BackendModulemanagerModel::getActiveModules('N',$exclude);
		$this->installedModules = BackendModulemanagerModel::getInstalledModules($exclude);
	}
	
	/**
	 * Filter out the non installed modules
	 *
	 * @return	void
	 */
	private function compareModules()
	{
		// loop modules
		foreach($this->moduleFolders as $module)
		{
			// not required nor hidden
			if(!in_array($module, $this->installedModules))
			{
				// add to the list of optional installs
				$this->nonInstalledModules[] = array('name' => $module);
			}
		}
	}
	
	/**
	 * Parse the datagrid and the reports
	 *
	 * @return	void
	 */
	protected function parse()
	{
		$this->tpl->assign('dgNonInstalled', ($this->dgNonInstalled->getNumResults() != 0) ? $this->dgNonInstalled->getContent() : false);
		$this->tpl->assign('dgInstalledActive', ($this->dgInstalledActive->getNumResults() != 0) ? $this->dgInstalledActive->getContent() : false);
		$this->tpl->assign('dgInstalledNonActive', ($this->dgInstalledNonActive->getNumResults() != 0) ? $this->dgInstalledNonActive->getContent() : false);
	}
}

?>