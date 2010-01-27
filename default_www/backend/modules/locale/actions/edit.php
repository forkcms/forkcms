<?php

/**
 * BackendLocaleEdit
 *
 * This is the edit action, it will display a form to edit an existing label.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendLocaleEdit extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if(BackendLocaleModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the datagrid
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}


	/**
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		$this->record = BackendLocaleModel::get($this->id);
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');
		$this->frm->addDropDown('application', array('backend' => 'Backend', 'frontend' => 'Frontend'), $this->record['application']);
		$this->frm->addDropDown('module', BackendModel::getModulesForDropDown(false), $this->record['module']);
		$this->frm->addDropDown('type', BackendLocaleModel::getTypesForDropDown(), $this->record['type']);
		$this->frm->addTextField('name', $this->record['name']);
		$this->frm->addTextField('value', $this->record['value']);
		$this->frm->addButton('save', ucfirst(BL::getLabel('Save')), 'submit', 'inputButton button mainButton');
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign id, name
		$this->tpl->assign('id', $this->record['id']);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// required fields
			$this->frm->getField('name')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('value')->isFilled(BL::getError('FieldIsRequired'));

			// module should be 'core' for any other application than backend
			if($this->frm->getField('application')->getValue() != 'backend' && $this->frm->getField('module')->getValue() != 'core')
			{
				$this->frm->getField('module')->setError(BL::getError('ModuleHasToBeCore', $this->url->getModule()));
			}

			// action values may not contain spaces
//			if($this->frm->getField('type')->getValue() == '');

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$locale = array();
				$locale['application'] = $this->frm->getField('application')->getValue();
				$locale['module'] = $this->frm->getField('module')->getValue();
				$locale['type'] = $this->frm->getField('type')->getValue();
				$locale['name'] = $this->frm->getField('name')->getValue();
				$locale['value'] = $this->frm->getField('value')->getValue();

				// update item
				BackendLocaleModel::update($this->id, $locale);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=edited&var='. urlencode($locale['name']));
			}
		}
	}
}

?>