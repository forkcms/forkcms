<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action, it will display an overview of all the translations with an inline edit option.
 *
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 */
class BackendLocaleIndex extends BackendBaseActionIndex
{
	/**
	 * Filter variables
	 *
	 * @var	array
	 */
	private $filter;

	/**
	 * Is God?
	 *
	 * @var	bool
	 */
	private $isGod;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->isGod = BackendAuthentication::getUser()->isGod();
		$this->setFilter();
		$this->loadForm();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the datagrid
	 */
	private function loadDataGrid()
	{
		// init vars
		$langWidth = (80 / count($this->filter['language']));

		// get all the translations for the selected languages
		$translations = BackendLocaleModel::getTranslations($this->filter['application'], $this->filter['module'], $this->filter['type'], $this->filter['language'], $this->filter['name'], $this->filter['value']);

		// create datagrids
		$this->dgLabels = new BackendDataGridArray(isset($translations['lbl']) ? $translations['lbl'] : array());
		$this->dgMessages = new BackendDataGridArray(isset($translations['msg']) ? $translations['msg'] : array());
		$this->dgErrors = new BackendDataGridArray(isset($translations['err']) ? $translations['err'] : array());
		$this->dgActions = new BackendDataGridArray(isset($translations['act']) ? $translations['act'] : array());

		// put the datagrids (references) in an array so we can loop them
		$dataGrids = array('lbl' => &$this->dgLabels, 'msg' => &$this->dgMessages, 'err' => &$this->dgErrors, 'act' => &$this->dgActions);

		// loop the datagrids (as references)
		foreach($dataGrids as $type => &$dataGrid)
		{
			// set sorting
			$dataGrid->setSortingColumns(array('module', 'name'), 'name');

			// disable paging
			$dataGrid->setPaging(false);

			// set header label for reference code
			$dataGrid->setHeaderLabels(array('name' => SpoonFilter::ucfirst(BL::lbl('ReferenceCode'))));

			// set column attributes for each language
			foreach($this->filter['language'] as $lang)
			{
				// add a class for the inline edit
				$dataGrid->setColumnAttributes($lang, array('class' => 'translationValue'));

				// add attributes, so the inline editing has all the needed data
				$dataGrid->setColumnAttributes($lang, array('data-id' => '{language: \'' . $lang . '\', application: \'' . $this->filter['application'] . '\', module: \'[module]\', name: \'[name]\', type: \'' . $type . '\'}'));

				// escape the double quotes
				$dataGrid->setColumnFunction(array('SpoonFilter', 'htmlentities'), array('[' . $lang . ']', null, ENT_QUOTES), $lang, true);
				if($type == 'act') $dataGrid->setColumnFunction('urldecode', array('[' . $lang . ']'), $lang, true);

				// set header labels
				$dataGrid->setHeaderLabels(array($lang => SpoonFilter::ucfirst(BL::getMessage(strtoupper($lang)))));

				// set column attributes
				$dataGrid->setColumnAttributes($lang, array('style' => 'width: ' . $langWidth . '%'));

				// hide translation_id column (only if only one language is selected because the key doesn't exist if more than 1 language is selected)
				if(count($this->filter['language']) == 1) $dataGrid->setColumnHidden('translation_id');

				// only 1 language selected?
				if(count($this->filter['language']) == 1)
				{
					// check if this action is allowed
					if(BackendAuthentication::isAllowedAction('edit'))
					{
						// add edit button
						$dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit', null, null, null) . '&amp;id=[translation_id]' . $this->filterQuery);
					}

					// check if this action is allowed
					if(BackendAuthentication::isAllowedAction('add'))
					{
						// add copy button
						$dataGrid->addColumnAction('copy', null, BL::lbl('Copy'), BackendModel::createURLForAction('add', null, null) . '&amp;id=[translation_id]' . $this->filterQuery, array('class' => 'button icon iconCopy linkButton'));
					}
				}
			}
		}
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');
		$this->frm->addDropdown('application', array('backend' => 'Backend', 'frontend' => 'Frontend'), $this->filter['application']);
		$this->frm->addText('name', $this->filter['name']);
		$this->frm->addText('value', $this->filter['value']);
		$this->frm->addMultiCheckbox('language', BackendLocaleModel::getLanguagesForMultiCheckbox($this->isGod), $this->filter['language'], 'noFocus');
		$this->frm->addMultiCheckbox('type', BackendLocaleModel::getTypesForMultiCheckbox(), $this->filter['type'], 'noFocus');
		$this->frm->addDropdown('module', BackendModel::getModulesForDropDown(false), $this->filter['module']);
		$this->frm->getField('module')->setDefaultElement(SpoonFilter::ucfirst(BL::lbl('ChooseAModule')));

		// manually parse fields
		$this->frm->parse($this->tpl);
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		parent::parse();

		// parse datagrids
		$this->tpl->assign('dgLabels', ($this->dgLabels->getNumResults() != 0) ? $this->dgLabels->getContent() : false);
		$this->tpl->assign('dgMessages', ($this->dgMessages->getNumResults() != 0) ? $this->dgMessages->getContent() : false);
		$this->tpl->assign('dgErrors', ($this->dgErrors->getNumResults() != 0) ? $this->dgErrors->getContent() : false);
		$this->tpl->assign('dgActions', ($this->dgActions->getNumResults() != 0) ? $this->dgActions->getContent() : false);

		// is filtered?
		if($this->getParameter('form', 'string', '') == 'filter') $this->tpl->assign('filter', true);

		// parse filter as query
		$this->tpl->assign('filter', $this->filterQuery);

		// parse isGod
		$this->tpl->assign('isGod', $this->isGod);

		// parse noItems, if all the datagrids are empty
		$this->tpl->assign('noItems', $this->dgLabels->getNumResults() == 0 && $this->dgMessages->getNumResults() == 0 && $this->dgErrors->getNumResults() == 0 && $this->dgActions->getNumResults() == 0);

		// parse the add URL
		$this->tpl->assign('addURL', BackendModel::createURLForAction('add', null, null, null) . $this->filterQuery);
	}

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
		// if no language is selected, set the working language as the selected
		if($this->getParameter('language', 'array') == null)
		{
			$_GET['language'] = array(BL::getWorkingLanguage());
			$this->parameters['language'] = array(BL::getWorkingLanguage());
		}

		// if no type is selected, set labels as the selected type
		if($this->getParameter('type', 'array') == null)
		{
			$_GET['type'] = array('lbl');
			$this->parameters['type'] = array('lbl', 'act', 'err', 'msg');
		}

		// set filter
		$this->filter['application'] = $this->getParameter('application') == null ? 'frontend' : $this->getParameter('application');
		$this->filter['module'] = $this->getParameter('module', 'string', null);
		$this->filter['type'] = $this->getParameter('type', 'array');
		$this->filter['language'] = $this->getParameter('language', 'array');
		$this->filter['name'] = $this->getParameter('name') == null ? '' : $this->getParameter('name');
		$this->filter['value'] = $this->getParameter('value') == null ? '' : $this->getParameter('value');

		// build query for filter
		$this->filterQuery = BackendLocaleModel::buildURLQueryByFilter($this->filter);;
	}
}
