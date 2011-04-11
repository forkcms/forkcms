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
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendFormBuilderModel::QRY_BROWSE, BL::getWorkingLanguage());

		// set headers
		$this->datagrid->setHeaderLabels(array('email' => ucfirst(BL::getLabel('Recipient')), 'sent_forms' => ''));

		// sorting columns
		$this->datagrid->setSortingColumns(array('name', 'email', 'method', 'sent_forms'), 'name');

		// set colum URLs
		$this->datagrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// set method label
		$this->datagrid->setColumnFunction(array('BackendFormBuilderModel', 'getLocale'), array('Method_[method]'), 'method');

		// set the amount of sent forms
		$this->datagrid->setColumnFunction(array('BackendFormBuilderIndex', 'parseNumForms'), array('[id]', '[sent_forms]'), 'sent_forms');

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::getLabel('Edit'));
	}


	/**
	 * Parse the datagrid and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
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

		// output
		return '<a href="' . BackendModel::createURLForAction('data') . '&amp;id=' . $formId . '" title="' . $output . '">' . $output . '</a>';
	}
}

?>