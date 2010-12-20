<?php

/**
 * BackendMailmotorAdd
 * This is the add-action, it will display a form to create a new mailing
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAdd extends BackendBaseActionAdd
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

		// validates the form
		$this->validateForm();

		// parse
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

		// fetch the campaigns
		$campaigns = BackendMailmotorModel::getCampaignsAsPairs();

		// fetch the groups
		$groups = BackendMailmotorModel::getGroupsForCheckboxes();

		// no groups set yet, so give the user a hand
		if(empty($groups)) $this->redirect(BackendModel::createURLForAction('add_group') .'&error=add-mailing-no-groups');

		// fetch the languages
		$languages = BackendMailmotorModel::getLanguagesForCheckboxes();

		// settings
		$this->frm->addText('name');
		if(count($campaigns) > 1) $this->frm->addDropdown('campaign', $campaigns);

		// sender
		$this->frm->addText('from_name', BackendModel::getModuleSetting('mailmotor', 'from_name'));
		$this->frm->addText('from_email', BackendModel::getModuleSetting('mailmotor', 'from_email'));

		// reply-to address
		$this->frm->addText('reply_to_email', BackendModel::getModuleSetting('mailmotor', 'reply_to_email'));

		// groups - if there is only 1 group present, we select it by default
		$this->frm->addMultiCheckbox('groups', $groups, ((count($groups) == 1 && isset($groups[0])) ? $groups[0]['value'] : false));

		// languages
		$this->frm->addRadiobutton('languages', $languages, 'nl');
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

			// shorten fields
			$txtName = $this->frm->getField('name');
			$txtFromName = $this->frm->getField('from_name');
			$txtFromEmail = $this->frm->getField('from_email');
			$txtReplyToEmail = $this->frm->getField('reply_to_email');
			$chkGroups = $this->frm->getField('groups');
			$rbtLanguages = $this->frm->getField('languages');

			// validate fields
			if($txtName->isFilled(BL::getError('NameIsRequired')))
			{
				if(BackendMailmotorModel::existsMailingByName($txtName->getValue())) $txtName->addError(BL::getError('MailingAlreadyExists'));
			}
			$txtFromName->isFilled(BL::getError('NameIsRequired'));
			$txtFromEmail->isFilled(BL::getError('EmailIsRequired'));
			$txtReplyToEmail->isFilled(BL::getError('EmailIsRequired'));

			// set form values
			$values = $this->frm->getValues();

			// check if at least one recipient group is chosen
			if(empty($values['groups'])) $chkGroups->addError(BL::getError('ChooseAtLeastOneGroup'));
			else
			{
				// fetch the recipients for these groups
				$recipients = BackendMailmotorModel::getAddressesByGroupID($values['groups']);

				// if no recipients were found, throw an error
				if(empty($recipients)) $chkGroups->addError(BL::getError('GroupsNoRecipients'));
			}

			// check if at least one language is chosen
			if(empty($values['languages'])) $rbtLanguages->isFilled(BL::getError('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// set values
				$variables = array();
				$variables['name'] = $txtName->getValue();
				$variables['from_name'] = $txtFromName->getValue();
				$variables['from_email'] = $txtFromEmail->getValue();
				$variables['reply_to_email'] = $txtReplyToEmail->getValue();
				$variables['language'] = $rbtLanguages->getValue();
				$variables['status'] = 'concept';
				$variables['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
				$variables['edited_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
				if(!empty($values['campaign'])) $variables['campaign_id'] = $this->frm->getField('campaign')->getValue();

				// insert the concept
				$id = BackendMailmotorModel::insertMailing($variables);

				// update the groups for this mailing
				BackendMailmotorModel::updateGroupsForMailing($id, $values['groups']);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit') .'&id='. $id .'&step=2');
			}
		}
	}
}

?>