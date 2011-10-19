<?php

/**
 * This is the index-action (default), it will display the overview
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the datagrid
		$this->loadDataGrid();

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
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendFormBuilderModel::QRY_BROWSE, BL::getWorkingLanguage());

		// set headers
		$this->dataGrid->setHeaderLabels(array('email' => ucfirst(BL::getLabel('Recipient')), 'sent_forms' => ''));

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name', 'email', 'method', 'sent_forms'), 'name');

		// set method label
		$this->dataGrid->setColumnFunction(array('BackendFormBuilderModel', 'getLocale'), array('Method_[method]'), 'method');

		// set the amount of sent forms
		$this->dataGrid->setColumnFunction(array('BackendFormBuilderIndex', 'parseNumForms'), array('[id]', '[sent_forms]'), 'sent_forms');

		// action allowed
		if(BackendAuthentication::isAllowedAction('edit','form_builder'))
		{
			// set colum URLs
			$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
			
			// add edit column
			$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::getLabel('Edit'));
		}
	}


	/**
	 * Parse the datagrid and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		// check if allowed to add
		$this->tpl->assign('actionAddAllowed',BackendAuthentication::isAllowedAction('add','form_builder'));
		
		// add datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}


	/**
	 * Parse amount of forms sent for the datagrid
	 *
	 * @return	string
	 * @param	int $formId			Id of the form.
	 * @param	int $sentForms		Amount of sent forms.
	 */
	public static function parseNumForms($formId, $sentForms)
	{
		// redefine
		$formId = (int) $formId;
		$sentForms = (int) $sentForms;

		// one form sent
		if($sentForms == 1) $output = BL::getMessage('OneSentForm');

		// multiple forms sent
		elseif($sentForms > 1) $output = sprintf(BL::getMessage('SentForms'), $sentForms);

		// no forms sent
		else $output = sprintf(BL::getMessage('SentForms'), $sentForms);

		// action allowed
		if(BackendAuthentication::isAllowedAction('data','form_builder'))
		{
			// output
			return '<a href="' . BackendModel::createURLForAction('data') . '&amp;id=' . $formId . '" title="' . $output . '">' . $output . '</a>';
		}
		else return $output;
	}
}

?>