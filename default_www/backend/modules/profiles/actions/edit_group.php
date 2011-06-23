<?php

/**
 * This is the edit_group-action, it will display a form to edit an existing group.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendProfilesEditGroup extends BackendBaseActionEdit
{
	/**
	 * Info about the current group.
	 *
	 * @var array
	 */
	private $group;


	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendProfilesModel::existsGroup($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

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

		// no item found, redirect to index, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
	}


	/**
	 * Get the data for a question
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get general info
		$this->group = BackendProfilesModel::getGroup($this->id);
	}


	/**
	 * Load the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editGroup');

		// create elements
		$this->frm->addText('name', $this->group['name']);
	}


	/**
	 * Parse the form.
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign the active record and additional variables
		$this->tpl->assign('group', $this->group);
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

			// get fields
			$txtName = $this->frm->getField('name');

			// name filled in?
			if($txtName->isFilled(BL::getError('NameIsRequired')))
			{
				//name already exists?
				if(BackendProfilesModel::existsGroupName($txtName->getValue(), $this->id))
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

				// update values
				BackendProfilesModel::updateGroup($this->id, $values);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('groups') . '&report=group-saved&var=' . urlencode($values['name']) . '&highlight=row-' . $this->id);
			}
		}
	}
}

?>