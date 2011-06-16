<?php

/**
 * This is the edit_profile_group-action, it will display a form to add a profile to a group.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendProfilesEditProfileGroup extends BackendBaseActionEdit
{
	/**
	 * Info about a group membership.
	 *
	 * @var array
	 */
	private $profileGroup;


	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');
		$this->profileId = $this->getParameter('profile_id', 'int');

		// does the item exists
		if($this->id !== null && BackendProfilesModel::existsProfileGroup($this->id))
		{
			// does profile exists
			if($this->profileId !== null && BackendProfilesModel::exists($this->profileId))
			{
				// call parent, this will probably add some general CSS/JS or other required files
				parent::execute();

				// get data
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

			// no item found, throw an exception, because somebody is fucking with our URL
			else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
		}

		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data about group rights.
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get information
		$this->profileGroup = BackendProfilesModel::getProfileGroup($this->id);
	}


	/**
	 * Load the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// get group values for dropdown
		$ddmValues = BackendProfilesModel::getGroupsForDropDown($this->profileId, $this->id);

		// create form
		$this->frm = new BackendForm('editProfileGroup');

		// create elements
		$this->frm->addDropdown('group', $ddmValues, $this->profileGroup['group_id']);
		$this->frm->addDate('expiration_date', $this->profileGroup['expires_on']);
		$this->frm->addTime('expiration_time', ($this->profileGroup['expires_on'] !== null) ? date('H:i', $this->profileGroup['expires_on']) : '');

		// set default element
		$this->frm->getField('group')->setDefaultElement('');
	}


	/**
	 * Parse.
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign the active record and additional variables
		$this->tpl->assign('profileGroup', $this->profileGroup);
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
			$ddmGroup = $this->frm->getField('group');
			$txtExpirationDate = $this->frm->getField('expiration_date');
			$txtExpirationTime = $this->frm->getField('expiration_time');

			// fields filled?
			$ddmGroup->isFilled(BL::getError('GroupIsRequired'));
			if($txtExpirationDate->isFilled()) $txtExpirationDate->isValid(BL::getError('InvalidDate'));
			if($txtExpirationTime->isFilled()) $txtExpirationTime->isValid(BL::getError('InvalidTime'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$values['group_id'] = $ddmGroup->getSelected();

				// only format date if not empty
				if($txtExpirationDate->isFilled() && $txtExpirationTime->isFilled())
				{
					// format date
					$values['expires_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($txtExpirationDate, $txtExpirationTime));
				}

				// reset expiration date
				else $values['expires_on'] = null;

				// update values
				$id = BackendProfilesModel::updateProfileGroup($this->id, $values);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $this->profileId . '&report=membership-saved&var=' . urlencode($values['group_id']) . '&highlight=row-' . $this->id . '#tabGroups');
			}
		}
	}
}

?>
