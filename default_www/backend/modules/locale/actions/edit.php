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
	 * Filter variables
	 *
	 * @var	arra
	 */
	private $filter;


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

			// filter options
			$this->setFilter();

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

		// no item found, throw an exceptions, because somebody is fucking with our URL
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
		$this->frm = new BackendForm('edit', BackendModel::createURLForAction(null, null, null, array('language' => $this->filter['language'], 'application' => $this->filter['application'], 'module' => $this->filter['module'], 'type' => $this->filter['type'], 'name' => $this->filter['name'], 'value' => $this->filter['value'], 'id' => $this->id)));

		// create and add elements
		$this->frm->addDropDown('language', array('nl' => 'Nederlands', 'fr' => 'Frans', 'en' => 'Engels'), 'nl'); // @todo davy - opbouwen van een goeie lijst
		$this->frm->addDropDown('application', array('backend' => 'backend', 'frontend' => 'frontend'), $this->record['application']);
		$this->frm->addDropDown('module', BackendModel::getModulesForDropDown(false), $this->record['module']);
		$this->frm->addDropDown('type', BackendLocaleModel::getTypesForDropDown(), $this->record['type']);
		$this->frm->addTextField('name', $this->record['name']);
		$this->frm->addTextField('value', $this->record['value'], null, 'inputTextfield', 'inputTextfieldError', true);
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

		// parse filter
		$this->tpl->assign($this->filter);

		// assign id, name
		$this->tpl->assign('id', $this->record['id']);
	}

	// @todo davy - phpdoc
	private function setFilter()
	{
		$this->filter['language'] = $this->getParameter('language');
		$this->filter['application'] = $this->getParameter('application');
		$this->filter['module'] = $this->getParameter('module');
		$this->filter['type'] = $this->getParameter('type');
		$this->filter['name'] = $this->getParameter('name');
		$this->filter['value'] = $this->getParameter('value');
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

			// redefine fields
			$txtName = $this->frm->getField('name');
			$txtValue = $this->frm->getField('value');

			// name checks
			if($txtName->isFilled(BL::getError('FieldIsRequired')))
			{
				// allowed regex (a-z and 0-9)
				if($txtName->isValidAgainstRegexp('|^([a-z0-9])+$|i', BL::getError('InvalidName', 'locale'))) // @todo davy - foutmelding toevoegen
				{
					// first letter does not seem to be a capital one
					if(!in_array(substr($txtName->getValue(), 0, 1), range('A', 'Z'))) $txtName->setError(BL::getError('InvalidName', 'locale'));

					// syntax is completely fine
					else
					{
						// this name already exists in this language
						if(BackendLocaleModel::existsByName($txtName->getValue(), $this->frm->getField('type')->getValue(), $this->frm->getField('module')->getValue(), $this->frm->getField('language')->getValue(), $this->id))
						{
							$txtName->setError('Dit veld bestaat al in de database in deze taal & applicatie'); // @todo davy - foutmelding toevoegen
						}
					}
				}
			}

			// value checks
			if($txtValue->isFilled(BL::getError('FieldIsRequired')))
			{
				// in case this is a 'act' type, there are special rules concerning possible values
				if($this->frm->getField('type')->getValue() == 'act')
				{
					$txtValue->isValidAgainstRegexp('|^([a-z0-9\-\_])+$|', BL::getError('InvalidValue', 'locale')); // @todo davy - foutmelding toevoegen
				}
			}

			// module should be 'core' for any other application than backend
			if($this->frm->getField('application')->getValue() != 'backend' && $this->frm->getField('module')->getValue() != 'core')
			{
				$this->frm->getField('module')->setError(BL::getError('ModuleHasToBeCore', 'locale'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$locale = array();
				$locale['user_id'] = BackendAuthentication::getUser()->getUserId();
				$locale['language'] = $this->frm->getField('language')->getValue();
				$locale['application'] = $this->frm->getField('application')->getValue();
				$locale['module'] = $this->frm->getField('module')->getValue();
				$locale['type'] = $this->frm->getField('type')->getValue();
				$locale['name'] = $this->frm->getField('name')->getValue();
				$locale['value'] = $this->frm->getField('value')->getValue();
				$locale['edited_on'] = BackendModel::getUTCDate();

				// update item
				BackendLocaleModel::update($this->id, $locale);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index', null, null, array('language' => $this->filter['language'], 'application' => $this->filter['application'], 'module' => $this->filter['module'], 'type' => $this->filter['type'], 'name' => $this->filter['name'], 'value' => $this->filter['value'])) .'&report=edited&var='. urlencode($locale['name']));
			}
		}
	}
}

?>