<?php

/**
 * BackendTagsAdd
 *
 * This is the add-action, it will display a form to create a new tag
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendTagsAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

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
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addTextField('name');
		$this->frm->addButton('save', ucfirst(BL::getLabel('Save')), 'submit', 'inputButton button mainButton');
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
				$tag = array();
				$tag['name'] = $this->frm->getField('name')->getValue();

				// insert the item
				$id = BackendTagsModel::insertTag($this->frm->getField('name')->getValue(), BL::getWorkingLanguage());

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=added&var='. urlencode($tag['name']) .'&highlight=id-'. $id);
			}
		}
	}
}

?>