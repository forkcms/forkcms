<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit a mailing
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorEdit extends BackendBaseActionEdit
{
	/**
	 * Bool that represents if the plain-text box should be shown
	 *
	 * @var	bool
	 */
	private $showPlainTextBox = true;

	/**
	 * The step ID
	 *
	 * @var	int
	 */
	private $stepId;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// set force compile on because we're using multiple forms on 1 page
		$this->tpl->setForceCompile(true);

		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// get the step
		$this->stepId = SpoonFilter::getGetValue('step', array(1, 2, 3, 4), 1, 'int');

		// does the item exist
		if(BackendMailmotorModel::existsMailing($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();
			$this->getData();
			$this->loadWizardSteps();
			$this->{'loadStep' . $this->stepId}();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&amp;error=non-existing');
	}

	/**
	 * Get the data
	 * If a revision-id was specified in the URL we load the revision and not the actual data
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendMailmotorModel::getMailing($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record) || $this->record['status'] == 'sent') $this->redirect(BackendModel::createURLForAction('index') . '&amp;error=non-existing');
	}

	/**
	 * Load the confirmation dialog
	 */
	private function loadConfirmationDialog()
	{
		// load statistics
		$groups = BackendMailmotorModel::getGroupsByIds($this->record['groups']);

		// fetch the campaign
		$campaign = BackendMailmotorModel::getCampaign($this->record['campaign_id']);

		// fetch the template
		$template = BackendMailmotorModel::getTemplate($this->record['language'], $this->record['template']);

		// declare stats array
		$stats['recipients'] = count($this->record['recipients']);
		$stats['mailing'] = $this->record['name'];
		$stats['label_persons'] = ($stats['recipients'] > 1) ? BL::lbl('Persons', 'core') : BL::lbl('Person', 'core');

		// campaign was set
		if(!empty($campaign))
		{
			// set data
			$stats['message'] = BL::msg('RecipientStatisticsCampaign', $this->getModule());
			$stats['campaign'] = $campaign['name'];

			// assign the recipient statistics variable
			$this->tpl->assign('recipientStatistics', sprintf($stats['message'], $stats['mailing'], $stats['campaign'], $stats['recipients'], $stats['label_persons']));
		}

		// campaign was not set
		else
		{
			// set data
			$stats['message'] = BL::msg('RecipientStatisticsNoCampaign', $this->getModule());

			// assign the recipient statistics variable
			$this->tpl->assign('recipientStatistics', sprintf($stats['message'], $stats['mailing'], $stats['recipients'], $stats['label_persons']));
		}

		// add comma separator to groups
		if(!empty($groups))
		{
			// fetch the last key in this array
			$lastRecord = end($groups);

			// loop the groups
			foreach($groups as $key => &$group)
			{
				// add comma field to the groups if this is not the last item
				if($lastRecord['id'] != $key) $group['comma'] = true;
			}
		}

		// assign the groups to the template
		$this->tpl->assign('groups', $groups);

		// assign the template language
		$this->tpl->assign('templateLanguage', SpoonFilter::ucfirst(BL::lbl(strtoupper($template['language']))));

		// get the price settings
		$pricePerEmail = BackendModel::getModuleSetting($this->getModule(), 'price_per_email');
		$pricePerCampaign = BackendModel::getModuleSetting($this->getModule(), 'price_per_campaign');

		// parse the price total
		$this->tpl->assign('price', ($stats['recipients'] * $pricePerEmail) + $pricePerCampaign);
	}

	/**
	 * Load the form for step 1
	 */
	private function loadFormForStep1()
	{
		// create form
		$this->frm = new BackendForm('step1');

		// fetch the campaigns
		$campaigns = BackendMailmotorModel::getCampaignsAsPairs();

		// fetch the groups
		$groups = BackendMailmotorModel::getGroupsWithRecipientsForCheckboxes();

		// fetch the languages
		$languages = BackendMailmotorModel::getLanguagesForCheckboxes();

		// settings
		$this->frm->addText('name', $this->record['name']);
		if(count($campaigns) > 1) $this->frm->addDropdown('campaign', $campaigns, $this->record['campaign_id']);

		// sender
		$this->frm->addText('from_name', $this->record['from_name']);
		$this->frm->addText('from_email', $this->record['from_email']);

		// reply-to address
		$this->frm->addText('reply_to_email', $this->record['reply_to_email']);

		// groups
		$this->frm->addMultiCheckbox('groups', $groups, $this->record['groups']);

		// languages
		$this->frm->addRadiobutton('languages', $languages, $this->record['language']);

		// show the form
		$this->tpl->assign('step1', true);
	}

	/**
	 * Load the form for step 2
	 */
	private function loadFormForStep2()
	{
		// create form
		$this->frm = new BackendForm('step2');

		// fetch the templates
		$templates = BackendMailmotorModel::getTemplatesForCheckboxes($this->record['language']);

		// no templates found
		if(empty($templates)) $this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $this->id . '&amp;step=1&amp;error=no-templates');

		// loop the templates
		foreach($templates as &$record)
		{
			// reformat custom variables
			$record['variables'] = array('language' => $record['language']);

			// set selected template
			if($record['value'] == $this->record['template']) $record['variables']['selected'] = true;

			// unset the language field
			unset($record['language']);
		}

		// templates
		$this->frm->addRadiobutton('templates', $templates, (!empty($this->record['template']) ? $this->record['template'] : null));

		// show the form
		$this->tpl->assign('step2', true);
	}

	/**
	 * Load the form for step 3
	 */
	private function loadFormForStep3()
	{
		// check if we have to redirect back to step 2 (template is not set)
		if(empty($this->record['template'])) $this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $this->id . '&amp;step=2&amp;error=complete-step-2');

		// check if we should show the plain text box
		$this->showPlainTextBox = BackendModel::getModuleSetting($this->getModule(), 'plain_text_editable');

		// create form
		$this->frm = new BackendForm('step3');

		// subject
		$this->frm->addText('subject', $this->record['subject']);
		$this->frm->addTextarea('content_html', $this->record['content_html']);
		if($this->showPlainTextBox)
		{
			$this->frm->addTextarea('content_plain', $this->record['content_plain']);
			$this->frm->getField('content_plain')->setAttribute('style', 'width: 99%;');
		}

		// show the form
		$this->tpl->assign('step3', true);
	}

	/**
	 * Load the form for step 4
	 */
	private function loadFormForStep4()
	{
		// check if we have to redirect back to step 3 (HTML content is not set)
		if(empty($this->record['content_html'])) $this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $this->id . '&amp;step=3&amp;error=complete-step-3');

		// get preview URL
		$previewURL = BackendMailmotorModel::getMailingPreviewURL($this->record['id'], 'html', true);

		// check if the mailmotor is linked
		if(BackendModel::getURLForBlock($this->getModule(), 'detail') == BackendModel::getURL(404)) $previewURL = false;

		// parse the preview URL
		$this->tpl->assign('previewURL', $previewURL);

		// create form
		$this->frm = new BackendForm('step4');

		// subject
		$this->frm->addText('email');
		$this->frm->addDate('send_on_date', $this->record['send_on']);
		$this->frm->addTime('send_on_time', SpoonDate::getDate('H:i', $this->record['send_on']));

		// show the form
		$this->tpl->assign('step4', true);
	}

	/**
	 * Loads step one
	 */
	private function loadStep1()
	{
		// load the form
		$this->loadFormForStep1();

		// validate the form
		$this->validateFormForStep1();
	}

	/**
	 * Loads step two
	 */
	private function loadStep2()
	{
		// load the form
		$this->loadFormForStep2();

		// validate the form
		$this->validateFormForStep2();
	}

	/**
	 * Loads step three
	 */
	private function loadStep3()
	{
		// load the form
		$this->loadFormForStep3();
	}

	/**
	 * Loads step four
	 */
	private function loadStep4()
	{
		// load the confirmation dialog
		$this->loadConfirmationDialog();

		// load the form
		$this->loadFormForStep4();

		// validate the form
		$this->validateFormForStep4();
	}

	/**
	 * Loads the wizard
	 */
	private function loadWizardSteps()
	{
		// check if this template path exists
		$templatePath = SpoonDirectory::exists(BACKEND_MODULE_PATH . '/templates/' . $this->record['language'] . '/' . $this->record['template']);

		// set wizard values
		$wizard = array();
		$wizard[1] = array('id' => 1, 'label' => BL::lbl('WizardInformation'));
		$wizard[2] = array('id' => 2, 'label' => BL::lbl('WizardTemplate'));
		$wizard[3] = array('id' => 3, 'label' => BL::lbl('WizardContent'));
		$wizard[4] = array('id' => 4, 'label' => BL::lbl('WizardSend'));

		// load the appropriate selected classes
		$wizard[$this->stepId]['selected'] = true;

		// loop the wizard steps
		foreach($wizard as &$step)
		{
			// if the current active step equals this loop's ID + 1, this list item will need the beforeSelected class
			if(($step['id'] + 1) == $this->stepId) $step['beforeSelected'] = true;

			// make the step link visible if we already passed this step
			if($step['id'] <= $this->stepId) $step['stepLink'] = true;

			// make the step link visible if this is step 2
			if($step['id'] == 2) $step['stepLink'] = true;

			// make the step link visible if this is step 3 and the template is already set
			if($step['id'] == 3 && !empty($this->record['template']) && $templatePath) $step['stepLink'] = true;

			// make the step link visible if this is step 4 and the subject/content_html is already set
			if($step['id'] == 4 && !empty($this->record['content_html'])) $step['stepLink'] = true;
		}

		// assign iteration to the template
		$this->tpl->assign('wizard', $wizard);
	}

	/**
	 * Parse the active step's form
	 */
	protected function parse()
	{
		parent::parse();

		// assign the active record and additional variables
		$this->tpl->assign('mailing', $this->record);
	}

	/**
	 * Validate the form for step 1
	 */
	private function validateFormForStep1()
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
			if($txtName->isFilled(BL::err('NameIsRequired')))
			{
				if(BackendMailmotorModel::existsMailingByName($txtName->getValue()) && $txtName->getValue() != $this->record['name']) $txtName->addError(BL::err('MailingAlreadyExists'));
			}
			$txtFromName->isFilled(BL::err('NameIsRequired'));
			$txtFromEmail->isFilled(BL::err('EmailIsRequired'));
			$txtReplyToEmail->isFilled(BL::err('EmailIsRequired'));

			// set form values
			$values = $this->frm->getValues();

			// check if at least one recipient group is chosen
			if(empty($values['groups'])) $chkGroups->addError(BL::err('ChooseAtLeastOneGroup'));
			else
			{
				// fetch the recipients for these groups
				$recipients = BackendMailmotorModel::getAddressesByGroupID($values['groups']);

				// if no recipients were found, throw an error
				if(empty($recipients)) $chkGroups->addError(BL::err('GroupsNoRecipients'));
			}

			// check if at least one language is chosen
			if(empty($values['languages'])) $rbtLanguages->isFilled(BL::err('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// set values
				$item['id'] = $this->id;
				$item['name'] = $txtName->getValue();
				$item['from_name'] = $txtFromName->getValue();
				$item['from_email'] = $txtFromEmail->getValue();
				$item['reply_to_email'] = $txtReplyToEmail->getValue();
				$item['language'] = $rbtLanguages->getValue();
				$item['edited_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
				if(isset($values['campaign']) && (!empty($values['campaign']) || $values['campaign'] == 0)) $item['campaign_id'] = $this->frm->getField('campaign')->getValue();

				// update the concept
				BackendMailmotorModel::updateMailing($item);

				// update groups for this mailing
				BackendMailmotorModel::updateGroupsForMailing($this->id, $values['groups']);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_mailing_step1', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $item['id'] . '&amp;step=2');
			}
		}
	}

	/**
	 * Validate the form for step 2
	 */
	private function validateFormForStep2()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// shorten fields
			$rbtTemplates = $this->frm->getField('templates');

			// set form values
			$values = $this->frm->getValues();

			// check if at least one language is chosen
			if(empty($values['templates'])) $rbtTemplates->isFilled(BL::err('TemplateIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// set values
				$item['id'] = $this->id;
				$item['template'] = $rbtTemplates->getValue();
				$item['edited_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

				// update the concept
				BackendMailmotorModel::updateMailing($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_mailing_step2', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $item['id'] . '&amp;step=3');
			}
		}
	}

	/**
	 * Validate the form for step 4
	 */
	private function validateFormForStep4()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// shorten fields
			$txtEmail = $this->frm->getField('email');
			$txtSendOnDate = $this->frm->getField('send_on_date');
			$txtSendOnTime = $this->frm->getField('send_on_time');

			// validation
			if($txtEmail->isFilled(BL::err('FieldIsRequired')))
			{
				$txtEmail->isEmail(BL::err('EmailIsInvalid'));
			}
			$txtSendOnDate->isValid(BL::err('DateIsInvalid'));
			$txtSendOnTime->isValid(BL::err('TimeIsInvalid'));

			// no errors?
			if($this->frm->isCorrect())
			{
				/*
					the actual sending of a mailing happens in ajax/send_mailing.php
					This, however, is the point where a preview is sent to a specific address.
				*/

				BackendMailmotorCMHelper::sendPreviewMailing($this->id, $txtEmail->getValue());

				// build URL
				$url = BackendModel::createURLForAction('edit') . '&amp;id=' . $this->id . '&amp;step=4';

				// send the preview
				$this->redirect($url . '&amp;report=preview-sent&amp;var=' . $txtEmail->getValue());
			}
		}
	}
}
