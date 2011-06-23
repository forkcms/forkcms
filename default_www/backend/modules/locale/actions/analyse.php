<?php

/**
 * This is the analyse-action, it will display an overview of used locale.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Lowie Benoot <lowie@netlash.com>
 * @since		2.0
 */
class BackendLocaleAnalyse extends BackendBaseActionIndex
{
	/**
	 * DataGrid instances
	 *
	 * @var	BackendDataGridArray
	 */
	private $dgBackend,
			$dgFrontend;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrids
		$this->loadDataGrids();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Format a serialized path-array into something that is usable in a datagrid
	 *
	 * @return	string
	 * @param	string $files	The serialized array with the paths.
	 */
	public static function formatFilesList($files)
	{
		// redefine
		$files = (array) unserialize((string) $files);

		// no files
		if(empty($files)) return '';

		// start
		$return = '<ul>' . "\n";

		// loop files
		foreach($files as $file) $return .= '<li><code title="' . str_replace(PATH_WWW, '', $file) . '">' . wordwrap(str_replace(PATH_WWW, '', $file), 80, '<br />', true) . '</code></li>' . "\n";

		// end
		$return .= '</ul>';

		// cleanup
		return $return;
	}


	/**
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDataGrids()
	{
		/*
		 * Frontend datagrid
		 */
		// create datagrid
		$this->dgFrontend = new BackendDataGridArray(BackendLocaleModel::getNonExistingFrontendLocale(BL::getWorkingLanguage()));

		// overrule default URL
		$this->dgFrontend->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting columns
		$this->dgFrontend->setSortingColumns(array('language', 'application', 'module', 'type', 'name'), 'name');

		// set colum URLs
		$this->dgFrontend->setColumnURL('name', BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');

		// set column functions
		$this->dgFrontend->setColumnFunction(array(__CLASS__, 'formatFilesList'), '[used_in]', 'used_in', true);

		// add columns
		//$this->dgFrontend->addColumn('add', null, BL::lbl('Add'), BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]', BL::lbl('Add'));
		$this->dgFrontend->addColumn('translation', null, null, null, BL::lbl('Add'));

		// add a class for the inline edit
		$this->dgFrontend->setColumnAttributes('translation', array('class' => 'translationValue'));

		// add attributes, so the inline editing has all the needed data
		$this->dgFrontend->setColumnAttributes('translation', array('data-id' => '{language: \'[language]\', application: \'[application]\', module: \'[module]\', name: \'[name]\', type: \'[type]\'}'));
		$this->dgFrontend->setColumnAttributes('translation', array('style' => 'width: 150px'));

		// disable paging
		$this->dgFrontend->setPaging(false);

		/*
		 * Backend datagrid
		 */
		// create datagrid
		$this->dgBackend = new BackendDataGridArray(BackendLocaleModel::getNonExistingBackendLocale(BL::getWorkingLanguage()));

		// overrule default URL
		$this->dgBackend->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting columns
		$this->dgBackend->setSortingColumns(array('language', 'application', 'module', 'type', 'name'), 'name');

		// set column URLs
		$this->dgBackend->setColumnURL('name', BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');

		// set column functions
		$this->dgBackend->setColumnFunction(array(__CLASS__, 'formatFilesList'), '[used_in]', 'used_in', true);

		// add columns
		//$this->dgBackend->addColumn('add', null, BL::lbl('Add'), BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]', BL::lbl('Add'));
		$this->dgBackend->addColumn('translation', null, null, null, BL::lbl('Add'));

		// add a class for the inline edit
		$this->dgBackend->setColumnAttributes('translation', array('class' => 'translationValue'));

		// add attributes, so the inline editing has all the needed data
		$this->dgBackend->setColumnAttributes('translation', array('data-id' => '{language: \'[language]\', application: \'[application]\', module: \'[module]\', name: \'[name]\', type: \'[type]\'}'));
		$this->dgBackend->setColumnAttributes('translation', array('style' => 'width: 150px'));

		// disable paging
		$this->dgBackend->setPaging(false);
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse datagrid
		$this->tpl->assign('dgBackend', ($this->dgBackend->getNumResults() != 0) ? $this->dgBackend->getContent() : false);
		$this->tpl->assign('dgFrontend', ($this->dgFrontend->getNumResults() != 0) ? $this->dgFrontend->getContent() : false);

		// parse filter
		$this->tpl->assign('language', BackendLanguage::getWorkingLanguage());
	}
}

?>