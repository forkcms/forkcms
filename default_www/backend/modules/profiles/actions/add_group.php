<?php

/**
 * This is the add_group-action, it will display a form to add a group for profiles.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendProfilesAddGroup extends BackendBaseActionEdit
{
	/**
	 * Execute the action.
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

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('addGroup');

		// create elements
		$this->frm->addText('name');
	}


	/**
	 * Validate the form.
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

			// get field
			$txtName = $this->frm->getField('name');

			// name filled in?
			if($txtName->isFilled(BL::getError('NameIsRequired')))
			{
				// name exists?
				if(BackendProfilesModel::existsGroupName($txtName->getValue()))
				{
					// set error
					$txtName->addError(BL::getError('GroupNameExists'));
				}
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$values['name'] = $txtName->getValue();

				// insert values
				$id = BackendProfilesModel::insertGroup($values);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('groups') . '&report=group-added&var=' . urlencode($values['name']) . '&highlight=row-' . $id);
			}
		}
	}
}

?>