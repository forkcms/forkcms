<?php

/**
 * BackendLocaleAdd
 * This is the add action, it will display a form to add an item to the locale.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendLocaleAdd extends BackendBaseActionAdd
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

		// filter options
		$this->setFilter();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add', BackendModel::createURLForAction(null, null, null, array('language' => $this->filter['language'], 'application' => $this->filter['application'], 'module' => $this->filter['module'], 'type' => $this->filter['type'], 'name' => $this->filter['name'], 'value' => $this->filter['value'])));

		// create and add elements
		$this->frm->addDropdown('application', array('backend' => 'Backend', 'frontend' => 'Frontend'), $this->filter['application']);
		$this->frm->addDropdown('module', BackendModel::getModulesForDropDown(false), $this->filter['module']);
		$this->frm->addDropdown('type', BackendLocaleModel::getTypesForDropDown(), $this->filter['type']);
		$this->frm->addText('name', $this->filter['name']);
		$this->frm->addText('value', $this->filter['value'], null, null, null, true);
		$this->frm->addDropdown('language', BackendLanguage::getLocaleLanguages(), $this->filter['language']);
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
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		$this->filter['language'] = ($this->getParameter('language') != '') ? $this->getParameter('language') : BL::getInterfaceLanguage();
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
				if($txtName->isValidAgainstRegexp('|^([a-z0-9])+$|i', BL::getError('InvalidName')))
				{
					// first letter does not seem to be a capital one
					if(!in_array(substr($txtName->getValue(), 0, 1), range('A', 'Z'))) $txtName->setError(BL::getError('InvalidName'));

					// syntax is completely fine
					else
					{
						// this name already exists in this language
						if(BackendLocaleModel::existsByName($txtName->getValue(), $this->frm->getField('type')->getValue(), $this->frm->getField('module')->getValue(), $this->frm->getField('language')->getValue(), $this->frm->getField('application')->getValue()))
						{
							$txtName->setError(BL::getError('AlreadyExists'));
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
					$txtValue->isValidAgainstRegexp('|^([a-z0-9\-\_])+$|', BL::getError('InvalidValue'));
				}
			}

			// module should be 'core' for any other application than backend
			if($this->frm->getField('application')->getValue() != 'backend' && $this->frm->getField('module')->getValue() != 'core')
			{
				$this->frm->getField('module')->setError(BL::getError('ModuleHasToBeCore'));
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
				BackendLocaleModel::insert($locale);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index', null, null, array('language' => $this->filter['language'], 'application' => $this->filter['application'], 'module' => $this->filter['module'], 'type' => $this->filter['type'], 'name' => $this->filter['name'], 'value' => $this->filter['value'])) .'&report=added&var='. urlencode($locale['name']));
			}
		}
	}
}

?>