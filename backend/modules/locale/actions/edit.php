<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit action, it will display a form to edit an existing locale item.
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
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
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendLocaleModel::exists($this->id))
		{
			parent::execute();
			$this->setFilter();
			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found or the user is not god , throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		$this->record = BackendLocaleModel::get($this->id);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('edit', BackendModel::createURLForAction(null, null, null, array('id' => $this->id)) . $this->filterQuery);
		$this->frm->addDropdown('application', array('backend' => 'backend', 'frontend' => 'frontend'), $this->record['application']);
		$this->frm->addDropdown('module', BackendModel::getModulesForDropDown(false), $this->record['module']);
		$this->frm->addDropdown('type', BackendLocaleModel::getTypesForDropDown(), $this->record['type']);
		$this->frm->addText('name', $this->record['name']);
		$this->frm->addTextarea('value', $this->record['value'], null, 'inputText', 'inputTextError', true);
		$this->frm->addDropdown('language', BackendLanguage::getWorkingLanguages(), $this->record['language']);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// prevent XSS
		$filter = SpoonFilter::arrayMapRecursive('htmlspecialchars', $this->filter);

		// parse filter
		$this->tpl->assign($filter);
		$this->tpl->assign('filterQuery', $this->filterQuery);

		// assign id, name
		$this->tpl->assign('name', $this->record['name']);
		$this->tpl->assign('id', $this->record['id']);
	}

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
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
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
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
					if(urlencode($txtValue->getValue()) != SpoonFilter::urlise($txtValue->getValue())) $txtValue->addError(BL::err('InvalidValue'));
				}
			}

			// module should be 'core' for any other application than backend
			if($this->frm->getField('application')->getValue() != 'backend' && $this->frm->getField('module')->getValue() != 'core')
			{
				$this->frm->getField('module')->setError(BL::err('ModuleHasToBeCore', $this->getModule()));
			}

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

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index', null, null, null) . '&report=edited&var=' . urlencode($item['name']) . '&highlight=row-' . $item['id'] . $this->filterQuery);
			}
		}
	}
}
