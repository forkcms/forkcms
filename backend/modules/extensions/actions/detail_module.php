<?php

/**
 * This is the detail-action it will display the details of a module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendExtensionsDetailModule extends BackendBaseActionIndex
{
	/**
	 * Module we request the details of.
	 *
	 * @var string
	 */
	private $currentModule;

	/**
	 * Datagrids.
	 *
	 * @var BackendDataGrid
	 */
	private $dataGridCronjobs, $dataGridEvents;

	/**
	 * Information fetched from the info.xml.
	 *
	 * @var array
	 */
	private $information = array();

	/**
	 * List of warnings.
	 *
	 * @var	array
	 */
	private $warnings = array();

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

			// load data
			$this->loadData();

			// load datagrids
			$this->loadDataGridCronjobs();
			$this->loadDataGridEvents();

			// parse
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, redirect to index, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('modules') . '&error=non-existing');
	}

	/**
	 * Load the data.
	 * This will also set some warnings if needed.
	 */
	private function loadData()
	{
		// inform that the module is not installed yet
		if(!BackendExtensionsModel::isModuleInstalled($this->currentModule))
		{
			$this->warnings[] = array('message' => BL::getMessage('InformationModuleIsNotInstalled'));
		}

		// path to information file
		$pathInfoXml = BACKEND_MODULES_PATH . '/' . $this->currentModule . '/info.xml';

		// information needs to exists
		if(SpoonFile::exists($pathInfoXml))
		{
			try
			{
				// load info.xml
				$infoXml = @new SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);

				// convert xml to useful array
				$this->information = BackendExtensionsModel::processModuleXml($infoXml);

				// empty data (nothing useful)
				if(empty($this->information)) $this->warnings[] = array('message' => BL::getMessage('InformationFileIsEmpty'));

				// check if cronjobs are installed already
				if(isset($this->information['cronjobs']))
				{
					foreach($this->information['cronjobs'] as $cronjob)
					{
						if(!$cronjob['active']) $this->warnings[] = array('message' => BL::getError('CronjobsNotSet'));
						break;
					}
				}
			}

			// warning that the information file is corrupt
			catch(Exception $e)
			{
				$this->warnings[] = array('message' => BL::getMessage('InformationFileCouldNotBeLoaded'));
			}
		}

		// warning that the information file is missing
		else $this->warnings[] = array('message' => BL::getMessage('InformationFileIsMissing'));
	}

	/**
	 * Load the data grid which contains the cronjobs.
	 */
	private function loadDataGridCronjobs()
	{
		// no cronjobs = dont bother
		if(!isset($this->information['cronjobs'])) return;

		// create data grid
		$this->dataGridCronjobs = new BackendDataGridArray($this->information['cronjobs']);

		// hide columns
		$this->dataGridCronjobs->setColumnsHidden(array('minute', 'hour', 'day-of-month', 'month', 'day-of-week', 'action', 'description', 'active'));

		// add cronjob data column
		$this->dataGridCronjobs->addColumn('cronjob', BL::getLabel('Cronjob'), '[description]<br /><strong>[minute] [hour] [day-of-month] [month] [day-of-week]</strong> php ' . PATH_WWW . '/backend/cronjob.php module=<strong>' . $this->currentModule . '</strong> action=<strong>[action]</strong>', null, null, null, 0);

		// no paging
		$this->dataGridCronjobs->setPaging(false);
	}

	/**
	 * Load the data grid which contains the events.
	 */
	private function loadDataGridEvents()
	{
		// no hooks = dont bother
		if(!isset($this->information['events'])) return;

		// create data grid
		$this->dataGridEvents = new BackendDataGridArray($this->information['events']);

		// no paging
		$this->dataGridEvents->setPaging(false);
	}

	/**
	 * Parse.
	 */
	protected function parse()
	{
		parent::parse();

		// assign module data
		$this->tpl->assign('name', $this->currentModule);
		$this->tpl->assign('warnings', $this->warnings);
		$this->tpl->assign('information', $this->information);
		$this->tpl->assign('showExtensionsInstallModule', !BackendExtensionsModel::isModuleInstalled($this->currentModule) && BackendAuthentication::isAllowedAction('install_module'));

		// data grids
		$this->tpl->assign('dataGridEvents', (isset($this->dataGridEvents) && $this->dataGridEvents->getNumResults() > 0) ? $this->dataGridEvents->getContent() : false);
		$this->tpl->assign('dataGridCronjobs', (isset($this->dataGridCronjobs) && $this->dataGridCronjobs->getNumResults() > 0) ? $this->dataGridCronjobs->getContent() : false);
	}
}
