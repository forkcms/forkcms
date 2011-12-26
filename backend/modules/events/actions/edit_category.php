<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendEventsEditCategory extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendEventsModel::existsCategory($this->id))
		{
			parent::execute();
			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		$this->record = BackendEventsModel::getCategory($this->id);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editCategory');

		// create elements
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addCheckbox('is_default', (BackendModel::getModuleSetting('blog', 'default_category_' . BL::getWorkingLanguage(), null) == $this->id));
		if((BackendModel::getModuleSetting('blog', 'default_category_' . BL::getWorkingLanguage(), null) == $this->id)) $this->frm->getField('is_default')->setAttribute('disabled', 'disabled');

		// meta object
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);

		// set callback for generating a unique URL
		$this->meta->setUrlCallback('BackendEventsModel', 'getURLForCategory', array($this->record['id']));
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();
		$this->tpl->assign('item', $this->record);

		// get default category id
		$defaultCategoryId = BackendModel::getModuleSetting('events', 'default_category_' . BL::getWorkingLanguage(), null);

		// get default category
		$defaultCategory = BackendEventsModel::getCategory($defaultCategoryId);

		// assign
		if($defaultCategoryId !== null) $this->tpl->assign('defaultCategory', $defaultCategory);

		// the default category may not be deleted
		if($defaultCategoryId != $this->id) $this->tpl->assign('deleteAllowed', true);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

			// validate meta
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['meta_id'] = $this->meta->save(true);

				// upate the item
				BackendEventsModel::updateCategory($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_category', array('item' => $item));
				
				// it isn't the default category but it should be.
				if(BackendModel::getModuleSetting('events', 'default_category_' . BL::getWorkingLanguage(), null) != $item['id'] && $this->frm->getField('is_default')->getChecked())
				{
					// store
					BackendModel::setModuleSetting('events', 'default_category_' . BL::getWorkingLanguage(), $item['id']);
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('categories') . '&report=edited-category&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
