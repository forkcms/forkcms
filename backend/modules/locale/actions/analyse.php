<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the analyse-action, it will display an overview of used locale.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 */
class BackendLocaleAnalyse extends BackendBaseActionIndex
{
	/**
	 * DataGrid instances
	 *
	 * @var	BackendDataGridArray
	 */
	private $dgBackend, $dgFrontend;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrids();
		$this->parse();
		$this->display();
	}

	/**
	 * Format a serialized path-array into something that is usable in a datagrid
	 *
	 * @param string $files The serialized array with the paths.
	 * @return string
	 */
	public static function formatFilesList($files)
	{
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
	 */
	private function loadDataGrids()
	{
		/*
		 * Frontend datagrid
		 */
		$this->dgFrontend = new BackendDataGridArray(BackendLocaleModel::getNonExistingFrontendLocale(BL::getWorkingLanguage()));

		// overrule default URL
		$this->dgFrontend->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting columns
		$this->dgFrontend->setSortingColumns(array('language', 'application', 'module', 'type', 'name'), 'name');

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('add'))
		{
			// set colum URLs
			$this->dgFrontend->setColumnURL('name', BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');
		}

		// set column functions
		$this->dgFrontend->setColumnFunction(array(__CLASS__, 'formatFilesList'), '[used_in]', 'used_in', true);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('save_translation'))
		{
			// add columns
			$this->dgFrontend->addColumn('translation', null, null, null, BL::lbl('Add'));

			// add a class for the inline edit
			$this->dgFrontend->setColumnAttributes('translation', array('class' => 'translationValue'));

			// add attributes, so the inline editing has all the needed data
			$this->dgFrontend->setColumnAttributes('translation', array('data-id' => '{language: \'[language]\', application: \'[application]\', module: \'[module]\', name: \'[name]\', type: \'[type]\'}'));
			$this->dgFrontend->setColumnAttributes('translation', array('style' => 'width: 150px'));
		}

		// disable paging
		$this->dgFrontend->setPaging(false);

		/*
		 * Backend datagrid
		 */
		$this->dgBackend = new BackendDataGridArray(BackendLocaleModel::getNonExistingBackendLocale(BL::getWorkingLanguage()));

		// overrule default URL
		$this->dgBackend->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting columns
		$this->dgBackend->setSortingColumns(array('language', 'application', 'module', 'type', 'name'), 'name');

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('add'))
		{
			// set column URLs
			$this->dgBackend->setColumnURL('name', BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');
		}

		// set column functions
		$this->dgBackend->setColumnFunction(array(__CLASS__, 'formatFilesList'), '[used_in]', 'used_in', true);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('save_translation'))
		{
			// add columns
			$this->dgBackend->addColumn('translation', null, null, null, BL::lbl('Add'));

			// add a class for the inline edit
			$this->dgBackend->setColumnAttributes('translation', array('class' => 'translationValue'));

			// add attributes, so the inline editing has all the needed data
			$this->dgBackend->setColumnAttributes('translation', array('data-id' => '{language: \'[language]\', application: \'[application]\', module: \'[module]\', name: \'[name]\', type: \'[type]\'}'));
			$this->dgBackend->setColumnAttributes('translation', array('style' => 'width: 150px'));
		}

		// disable paging
		$this->dgBackend->setPaging(false);
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		parent::parse();

		// parse datagrid
		$this->tpl->assign('dgBackend', ($this->dgBackend->getNumResults() != 0) ? $this->dgBackend->getContent() : false);
		$this->tpl->assign('dgFrontend', ($this->dgFrontend->getNumResults() != 0) ? $this->dgFrontend->getContent() : false);

		// parse filter
		$this->tpl->assign('language', BackendLanguage::getWorkingLanguage());
	}
}
