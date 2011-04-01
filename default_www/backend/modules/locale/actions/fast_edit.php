<?php

/**
 * This is the fast edit-action, it will display an overview of all the translations, with an inline edit.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Lowie Benoot <lowie@netlash.com>
 * @since		2.0
 */
class BackendLocaleFastEdit extends BackendBaseActionIndex
{
	/**
	 * Filter variables
	 *
	 * @var	array
	 */
	private $filter;


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
		$langWidth = (80 / count($this->filter['selected_languages']));

		// get all the translations for the selected languages
		$translations = BackendLocaleModel::getTranslationsForLanguages($this->filter['selected_languages'], $this->filter['application'], $this->filter['name']);

		// create datagrids
		$this->dgLabels = new BackendDataGridArray(isset($translations['lbl']) ? $translations['lbl'] : array());
		$this->dgMessages = new BackendDataGridArray(isset($translations['msg']) ? $translations['msg'] : array());
		$this->dgErrors = new BackendDataGridArray(isset($translations['err']) ? $translations['err'] : array());
		$this->dgActions = new BackendDataGridArray(isset($translations['act']) ? $translations['act'] : array());

		// put the datagrids (references) in an array so we can loop them
		$datagrids = array(&$this->dgLabels, &$this->dgMessages, &$this->dgErrors, &$this->dgActions);

		// loop the datagrids
		foreach($datagrids as &$datagrid)
		{
			// create datagrids
			$datagrid = new BackendDataGridArray(isset($translations['lbl']) ? $translations['lbl'] : array());

			// set sorting
			$datagrid->setSortingColumns(array('module', 'name'), 'name');

			// disable paging
			$datagrid->setPaging(false);

			// set column attributes for each language
			foreach($this->filter['selected_languages'] as $lang)
			{
				// add a class for the inline edit
				$datagrid->setColumnAttributes($lang, array('class' => 'translationValue'));

				// add attributes, so the inline editing has all the needed data
				$datagrid->setColumnAttributes($lang, array('data-id' => '{language: \'' . $lang . '\', application: \'' . $this->filter['application'] . '\', module: \'[module]\', name: \'[name]\', type: \'lbl\'}'));

				// escape the double quotes
				$datagrid->setColumnFunction(array('SpoonFilter', 'htmlentities'), array('[' . $lang . ']', null, ENT_QUOTES), $lang, true);

				// set header labels
				$datagrid->setHeaderLabels(array($lang => ucfirst(BL::getLabel(strtoupper($lang)))));

				// set column attributes
				$datagrid->setColumnAttributes($lang, array('style' => 'width: '. $langWidth .'%'));
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
		// get languages
		$aLanguages = BL::getWorkingLanguages();

		// create a new array to redefine the langauges for the multicheckbox
		$languages = array();

		// loop the languages
		foreach($aLanguages as $key => $lang)
		{
			$languages[$key]['value'] = $key;
			$languages[$key]['label'] = $lang;
		}

		// create form
		$this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');

		// add fields
		$this->frm->addDropdown('application', array('backend' => 'Backend', 'frontend' => 'Frontend'), $this->filter['application']);
		$this->frm->addText('name', $this->filter['name']);
		$this->frm->addMultiCheckbox('languages', $languages, $this->filter['selected_languages']);

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
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		$this->filter['selected_languages'] = $this->getParameter('languages', 'array') == null ? array(BL::getWorkingLanguage()) : $this->getParameter('languages', 'array');
		$this->filter['name'] = $this->getParameter('name', 'string') == null ? '' : $this->getParameter('name', 'string');
		$this->filter['application'] = $this->getParameter('application') == null ? 'frontend' : $this->getParameter('application');
	}
}

?>