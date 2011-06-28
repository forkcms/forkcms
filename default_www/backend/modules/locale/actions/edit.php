<?php

/**
 * This is the edit action, it will display a form to edit an existing locale item.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Lowie Benoot <lowie@netlash.com>
 * @since		2.0
 */
class BackendLocaleEdit extends BackendBaseActionEdit
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
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendLocaleModel::exists($this->id) && BackendAuthentication::getUser()->isGod())
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

			// parse
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found or the user is not god , throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
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
		$this->frm = new BackendForm('edit', BackendModel::createURLForAction(null, null, null, array('id' => $this->id)) . $this->filterQuery);

		// create and add elements
		$this->frm->addDropdown('application', array('backend' => 'backend', 'frontend' => 'frontend'), $this->record['application']);
		$this->frm->addDropdown('module', BackendModel::getModulesForDropDown(false), $this->record['module']);
		$this->frm->addDropdown('type', BackendLocaleModel::getTypesForDropDown(), $this->record['type']);
		$this->frm->addText('name', $this->record['name']);
		$this->frm->addText('value', $this->record['value'], null, 'inputText', 'inputTextError', true);
		$this->frm->addDropdown('language', BackendLanguage::getLocaleLanguages(), $this->record['language']);
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
		$this->tpl->assign('filterQuery', $this->filterQuery);


		// assign id, name
		$this->tpl->assign('name', $this->record['name']);
		$this->tpl->assign('id', $this->record['id']);
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		// get filter values
		$this->filter['language'] = ($this->getParameter('language', 'array') != '') ? $this->getParameter('language', 'array') : BL::getWorkingLanguage();
		$this->filter['application'] = $this->getParameter('application');
		$this->filter['module'] = $this->getParameter('module');
		$this->filter['type'] = $this->getParameter('type', 'array');
		$this->filter['name'] = $this->getParameter('name');
		$this->filter['value'] = $this->getParameter('value');

		// build query for filter
		$this->filterQuery = BackendLocaleModel::buildURLQueryByFilter($this->filter);
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
			if($txtName->isFilled(BL::err('FieldIsRequired')))
			{
				// allowed regex (a-z and 0-9)
				if($txtName->isValidAgainstRegexp('|^([a-z0-9])+$|i', BL::err('InvalidName')))
				{
					// first letter does not seem to be a capital one
					if(!in_array(substr($txtName->getValue(), 0, 1), range('A', 'Z'))) $txtName->setError(BL::err('InvalidName'));

					// syntax is completely fine
					else
					{
						// check if exists
						if(BackendLocaleModel::existsByName($txtName->getValue(), $this->frm->getField('type')->getValue(), $this->frm->getField('module')->getValue(), $this->frm->getField('language')->getValue(), $this->frm->getField('application')->getValue(), $this->id))
						{
							$txtName->setError(BL::err('AlreadyExists'));
						}
					}
				}
			}

			// value checks
			if($txtValue->isFilled(BL::err('FieldIsRequired')))
			{
				// in case this is a 'act' type, there are special rules concerning possible values
				if($this->frm->getField('type')->getValue() == 'act')
				{
					$txtValue->isValidAgainstRegexp('|^([a-z0-9\-\_])+$|', BL::err('InvalidValue'));
				}
			}

			// module should be 'core' for any other application than backend
			if($this->frm->getField('application')->getValue() != 'backend' && $this->frm->getField('module')->getValue() != 'core')
			{
				$this->frm->getField('module')->setError(BL::err('ModuleHasToBeCore', 'locale'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['language'] = $this->frm->getField('language')->getValue();
				$item['application'] = $this->frm->getField('application')->getValue();
				$item['module'] = $this->frm->getField('module')->getValue();
				$item['type'] = $this->frm->getField('type')->getValue();
				$item['name'] = $this->frm->getField('name')->getValue();
				$item['value'] = $this->frm->getField('value')->getValue();
				$item['edited_on'] = BackendModel::getUTCDate();

				// update item
				BackendLocaleModel::update($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index', null, null, null) . '&report=edited&var=' . urlencode($item['name']) . '&highlight=row-' . $item['id'] . $this->filterQuery);
			}
		}
	}
}

?>