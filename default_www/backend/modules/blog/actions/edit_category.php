<?php

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
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
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
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
		$this->frm->addText('title', $this->record['title']);
		$this->frm->addCheckbox('is_default', (BackendModel::getModuleSetting('blog', 'default_category_' . BL::getWorkingLanguage(), null) == $this->id));
		if((BackendModel::getModuleSetting('blog', 'default_category_' . BL::getWorkingLanguage(), null) == $this->id)) $this->frm->getField('is_default')->setAttribute('disabled', 'disabled');

		// meta object
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
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

		// assign
		$this->tpl->assign('item', $this->record);

		// get default category id
		$defaultCategoryId = BackendModel::getModuleSetting('blog', 'default_category_' . BL::getWorkingLanguage(), null);

		// get default category
		$defaultCategory = BackendBlogModel::getCategory($defaultCategoryId);

		// assign
		if($defaultCategoryId !== null) $this->tpl->assign('defaultCategory', $defaultCategory);

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
			// set callback for generating an unique URL
			$this->meta->setUrlCallback('BackendBlogModel', 'getURLForCategory', array($this->record['id']));

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['meta_id'] = $this->meta->save(true);

				// upate the item
				BackendBlogModel::updateCategory($item);

				// it isn't the default category but it should be.
				if(BackendModel::getModuleSetting('blog', 'default_category_' . BL::getWorkingLanguage(), null) != $item['id'] && $this->frm->getField('is_default')->getChecked())
				{
					// store
					BackendModel::setModuleSetting('blog', 'default_category_' . BL::getWorkingLanguage(), $item['id']);
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('categories') . '&report=edited-category&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>