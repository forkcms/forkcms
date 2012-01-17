<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendFormBuilderIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the datagrids
	 */
	private function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(BackendFormBuilderModel::QRY_BROWSE, BL::getWorkingLanguage());
		$this->dataGrid->setHeaderLabels(array('email' => SpoonFilter::ucfirst(BL::getLabel('Recipient')), 'sent_forms' => ''));
		$this->dataGrid->setSortingColumns(array('name', 'email', 'method', 'sent_forms'), 'name');
		$this->dataGrid->setColumnFunction(array('BackendFormBuilderModel', 'formatRecipients'), array('[email]'), 'email');
		$this->dataGrid->setColumnFunction(array('BackendFormBuilderModel', 'getLocale'), array('Method_[method]'), 'method');
		$this->dataGrid->setColumnFunction(array('BackendFormBuilderIndex', 'parseNumForms'), array('[id]', '[sent_forms]'), 'sent_forms');

		// check if edit action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
			$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::getLabel('Edit'));
		}
	}

	/**
	 * Parse the datagrid and the reports
	 */
	protected function parse()
	{
		parent::parse();

		// add datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}

	/**
	 * Parse amount of forms sent for the datagrid
	 *
	 * @param int $formId Id of the form.
	 * @param int $sentForms Amount of sent forms.
	 * @return string
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

		// check if data action is allowed
		if(BackendAuthentication::isAllowedAction('data', 'form_builder'))
		{
			// output
			$output = '<a href="' . BackendModel::createURLForAction('data') . '&amp;id=' . $formId . '" title="' . $output . '">' . $output . '</a>';
		}

		return $output;
	}
}
