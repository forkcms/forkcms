<?php

/**
 * This is the index-action, it will display an overview of all the translations, with an inline edit.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Lowie Benoot <lowie@netlash.com>
 * @since		2.1
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
	 * Build a query for the URL based on the filter
	 *
	 * @return	array
	 */
	private function buildURLQuery()
	{
		$query = '';

		foreach($this->filter as $key => $value)
		{
			// is it an array?
			if(is_array($value))
			{
				// loop the array
				foreach($value as $v)
				{
					// replace keys
					if($key == 'language') $key = 'languages';
					elseif($key == 'type') $key = 'translationTypes';

					// add to the query
					$query .= '&' . $key . '[]=' . $v;
				}
			}

			else
			{
				// add to the query
				$query .= '&' . $key . '=' . $value;
			}
		}

		return $query;
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// set filter
		$this->setFilter();

		// load form
		$this->loadForm();

		// load datagrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the datagrid
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// init vars
		$langWidth = (80 / count($this->filter['language']));

		// get the user
		$user = BackendAuthentication::getUser();

		// is the user a GodUser?
		$this->isGod = $user->isGod();

		// get all the translations for the selected languages
		$translations = BackendLocaleModel::getTranslations($this->filter['application'], $this->filter['module'], $this->filter['type'], $this->filter['language'], $this->filter['name'], $this->filter['value']);

		// create datagrids
		$this->dgLabels = new BackendDataGridArray(isset($translations['lbl']) ? $translations['lbl'] : array());
		$this->dgMessages = new BackendDataGridArray(isset($translations['msg']) ? $translations['msg'] : array());
		$this->dgErrors = new BackendDataGridArray(isset($translations['err']) ? $translations['err'] : array());
		$this->dgActions = new BackendDataGridArray(isset($translations['act']) ? $translations['act'] : array());

		// put the datagrids (references) in an array so we can loop them
		$datagrids = array('lbl' => &$this->dgLabels, 'msg' => &$this->dgMessages, 'err' => &$this->dgErrors, 'act' => &$this->dgActions);

		// loop the datagrids (as references)
		foreach($datagrids as $type => &$datagrid)
		{
			// set sorting
			$datagrid->setSortingColumns(array('module', 'name'), 'name');

			// disable paging
			$datagrid->setPaging(false);

			// set column attributes for each language
			foreach($this->filter['language'] as $lang)
			{
				// add a class for the inline edit
				$datagrid->setColumnAttributes($lang, array('class' => 'translationValue'));

				// add attributes, so the inline editing has all the needed data
				$datagrid->setColumnAttributes($lang, array('data-id' => '{language: \'' . $lang . '\', application: \'' . $this->filter['application'] . '\', module: \'[module]\', name: \'[name]\', type: \'' . $type . '\'}'));

				// escape the double quotes
				$datagrid->setColumnFunction(array('SpoonFilter', 'htmlentities'), array('[' . $lang . ']', null, ENT_QUOTES), $lang, true);

				// set header labels
				$datagrid->setHeaderLabels(array($lang => ucfirst(BL::getLabel(strtoupper($lang)))));

				// set column attributes
				$datagrid->setColumnAttributes($lang, array('style' => 'width: ' . $langWidth . '%'));

				// hide translation_id column (only if only one language is selected because the key doesn't exist if more than 1 language is selected)
				if(count($this->filter['language']) == 1) $datagrid->setColumnHidden('translation_id');

				// only 1 language selected?
				if(count($this->filter['language']) == 1)
				{
					// user is God?
					if($this->isGod)
					{
						//  add edit button
						$datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit', null, null, null) . '?&amp;id=[translation_id]' . $this->buildURLQuery());

						// add copy button
						$datagrid->addColumnAction('copy', null, BL::lbl('Copy'), BackendModel::createURLForAction('add', null, null) . '?&amp;id=[translation_id]' . $this->buildURLQuery(), array('class' => 'button icon iconCopy linkButton'));
					}
				}
			}
		}
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');

		// add fields
		$this->frm->addDropdown('application', array('backend' => 'Backend', 'frontend' => 'Frontend'), $this->filter['application']);
		$this->frm->addText('name', $this->filter['name']);
		$this->frm->addText('value', $this->filter['value']);
		$this->frm->addMultiCheckbox('languages', BackendLocaleModel::getLanguagesForMultiCheckbox(), $this->filter['language']);
		$this->frm->addMultiCheckbox('translationTypes', BackendLocaleModel::getTypesForMultiCheckbox(), $this->filter['type']);
		$this->frm->addDropdown('module', BackendModel::getModulesForDropDown(false), $this->filter['module']);
		$this->frm->getField('module')->setDefaultElement(ucfirst(BL::lbl('ChooseAModule')));

		// manually parse fields
		$this->frm->parse($this->tpl);
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse datagrids
		$this->tpl->assign('dgLabels', ($this->dgLabels->getNumResults() != 0) ? $this->dgLabels->getContent() : false);
		$this->tpl->assign('dgMessages', ($this->dgMessages->getNumResults() != 0) ? $this->dgMessages->getContent() : false);
		$this->tpl->assign('dgErrors', ($this->dgErrors->getNumResults() != 0) ? $this->dgErrors->getContent() : false);
		$this->tpl->assign('dgActions', ($this->dgActions->getNumResults() != 0) ? $this->dgActions->getContent() : false);

		// is filtered?
		if($this->getParameter('form', 'string', '') == 'filter') $this->tpl->assign('filter', true);

		// parse filter
		$this->tpl->assign($this->filter);

		// parse filter as query
		$this->tpl->assign('filter', $this->buildURLQuery());

		// parse the first element of the filter language and type (because it's an array)
		$this->tpl->assign('type', $this->filter['type'][0]);
		$this->tpl->assign('language', $this->filter['language'][0]);

		// parse isGod
		$this->tpl->assign('isGod', $this->isGod);

	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		// if no language is selected, set the working language as the selected
		if($this->getParameter('languages', 'array') == null)
		{
			$_GET['languages'] = array(BL::getWorkingLanguage());
			$this->parameters['languages'] = array(BL::getWorkingLanguage());
		}

		// if no type is selected, set labels as the selected type
		if($this->getParameter('translationTypes', 'array') == null)
		{
			$_GET['translationTypes'] = array('lbl');
			$this->parameters['translationTypes'] = array('lbl');
		}

		// set filter
		$this->filter['application'] = $this->getParameter('application') == null ? 'backend' : $this->getParameter('application');
		$this->filter['module'] = $this->getParameter('module');
		$this->filter['type'] = $this->getParameter('translationTypes', 'array');
		$this->filter['language'] = $this->getParameter('languages', 'array');
		$this->filter['name'] = $this->getParameter('name') == null ? '' : $this->getParameter('name');
		$this->filter['value'] = $this->getParameter('value') == null ? '' : $this->getParameter('value');
	}
}

?>