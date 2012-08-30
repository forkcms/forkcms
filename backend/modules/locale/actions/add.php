<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add action, it will display a form to add an item to the locale.
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
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
	 */
	public function execute()
	{
		parent::execute();
		$this->setFilter();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		if($this->getParameter('id') != null)
		{
			// get the translation
			$translation = BackendLocaleModel::get($this->getParameter('id', 'int'));

			// if not empty, set the filter
			if(!empty($translation))
			{
				// we are copying the given translation
				$isCopy = true;
			}

			// this translation doesn't exist
			else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing' . $this->filterQuery);
		}

		// not copying
		else $isCopy = false;

		// create form
		$this->frm = new BackendForm('add', BackendModel::createURLForAction() . $this->filterQuery);

		// create and add elements
		$this->frm->addDropdown('application', array('backend' => 'Backend', 'frontend' => 'Frontend'), $isCopy ? $translation['application'] : $this->filter['application']);
		$this->frm->addDropdown('module', BackendModel::getModulesForDropDown(false), $isCopy ? $translation['module'] : $this->filter['module']);
		$this->frm->addDropdown('type', BackendLocaleModel::getTypesForDropDown(), $isCopy ? $translation['type'] : $this->filter['type'][0]);
		$this->frm->addText('name', $isCopy ? $translation['name'] : $this->filter['name']);
		$this->frm->addTextarea('value', $isCopy ? $translation['value'] : $this->filter['value'], null, null, null, true);
		$this->frm->addDropdown('language', BackendLanguage::getWorkingLanguages(), $isCopy ? $translation['language'] : $this->filter['language'][0]);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// prevent XSS
		$filter = SpoonFilter::arrayMapRecursive('htmlspecialchars', $this->filter);

		$this->tpl->assign($filter);
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
		$this->filterQuery = '&' . http_build_query($this->filter);
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
						// this name already exists in this language
						if(BackendLocaleModel::existsByName($txtName->getValue(), $this->frm->getField('type')->getValue(), $this->frm->getField('module')->getValue(), $this->frm->getField('language')->getValue(), $this->frm->getField('application')->getValue()))
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
				$this->frm->getField('module')->setError(BL::err('ModuleHasToBeCore'));
			}

			if($this->frm->isCorrect())
			{
				// build item
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['language'] = $this->frm->getField('language')->getValue();
				$item['application'] = $this->frm->getField('application')->getValue();
				$item['module'] = $this->frm->getField('module')->getValue();
				$item['type'] = $this->frm->getField('type')->getValue();
				$item['name'] = $this->frm->getField('name')->getValue();
				$item['value'] = $this->frm->getField('value')->getValue();
				$item['edited_on'] = BackendModel::getUTCDate();

				// update item
				$item['id'] = BackendLocaleModel::insert($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index', null, null, null) . '&report=added&var=' . urlencode($item['name']) . '&highlight=row-' . $item['id'] . $this->filterQuery);
			}
		}
	}
}
