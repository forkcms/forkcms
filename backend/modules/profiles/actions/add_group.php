<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add_group-action, it will display a form to add a group for profiles.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendProfilesAddGroup extends BackendBaseActionEdit
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('addGroup');
		$this->frm->addText('name');
	}

	/**
	 * Validate the form.
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

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add_group', array('item' => $values));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('groups') . '&report=group-added&var=' . urlencode($values['name']) . '&highlight=row-' . $id);
			}
		}
	}
}
