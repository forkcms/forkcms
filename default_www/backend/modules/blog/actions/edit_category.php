<?php

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogEditCategory extends BackendBaseActionEdit
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
		if($this->id !== null && BackendBlogModel::existsCategory($this->id))
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
		$this->record = BackendBlogModel::getCategory($this->id);
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editCategory');

		// create elements
		$this->frm->addText('name', $this->record['name']);
		$this->frm->addCheckbox('is_default', (BackendModel::getModuleSetting('blog', 'default_category_'. BL::getWorkingLanguage(), null) == $this->id));
		if((BackendModel::getModuleSetting('blog', 'default_category_'. BL::getWorkingLanguage(), null) == $this->id)) $this->frm->getField('is_default')->setAttribute('disabled', 'disabled');
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
		$this->tpl->assign('name', $this->record['name']);

		// get default category id
		$defaultCategoryId = BackendModel::getModuleSetting('blog', 'default_category_'. BL::getWorkingLanguage(), null);

		// assign
		if($defaultCategoryId !== null) $this->tpl->assign('defaultCategory', BackendBlogModel::getCategory($defaultCategoryId));

		// the default category may not be deleted
		if($defaultCategoryId != $this->id) $this->tpl->assign('deleteAllowed', true);
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

			// validate fields
			$this->frm->getField('name')->isFilled(BL::getError('NameIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['name'] = $this->frm->getField('name')->getValue();
				$item['url'] = BackendBlogModel::getURLForCategory($item['name'], $this->id);

				// upate the item
				BackendBlogModel::updateCategory($item);

				// it isn't the default category but it should be.
				if(BackendModel::getModuleSetting('blog', 'default_category_'. BL::getWorkingLanguage(), null) != $item['id'] && $this->frm->getField('is_default')->getChecked())
				{
					// store
					BackendModel::setModuleSetting('blog', 'default_category_'. BL::getWorkingLanguage(), $item['id']);
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('categories') .'&report=edited-category&var='. urlencode($item['name']) .'&highlight=row-'. $item['id']);
			}
		}
	}
}

?>