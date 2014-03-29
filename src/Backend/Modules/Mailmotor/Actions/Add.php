<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;

/**
 * This is the add-action, it will display a form to create a new mailing
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Add extends BackendBaseActionAdd
{
    /**
     * Execute the action
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
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('add');

        // fetch the campaigns
        $campaigns = BackendMailmotorModel::getCampaignsAsPairs();

        // fetch the groups
        $groupIds = BackendMailmotorModel::getGroupIDs();
        $groups = BackendMailmotorModel::getGroupsWithRecipientsForCheckboxes();

        // no groups were made yet
        if (empty($groups) && empty($groupIds)) {
            $this->redirect(
                BackendModel::createURLForAction('AddGroup') . '&error=add-mailing-no-groups'
            );
        } elseif (empty($groups)) {
            // groups were made, but none have subscribers
            $this->redirect(
                BackendModel::createURLForAction('Addresses') . '&error=no-subscribers'
            );
        }

        // fetch the languages
        $languages = BackendMailmotorModel::getLanguagesForCheckboxes();

        // settings
        $this->frm->addText('name');
        if (count($campaigns) > 1) {
            $this->frm->addDropdown('campaign', $campaigns);
        }

        // sender
        $this->frm->addText('from_name', BackendModel::getModuleSetting($this->getModule(), 'from_name'));
        $this->frm->addText('from_email', BackendModel::getModuleSetting($this->getModule(), 'from_email'));

        // reply-to address
        $this->frm->addText('reply_to_email', BackendModel::getModuleSetting($this->getModule(), 'reply_to_email'));

        // groups - if there is only 1 group present, we select it by default
        $this->frm->addMultiCheckbox(
            'groups',
            $groups,
            ((count($groups) == 1 && isset($groups[0])) ? $groups[0]['value'] : false)
        );

        // languages
        $this->frm->addRadiobutton('languages', $languages, BL::getWorkingLanguage());
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
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
            $txtName->isFilled(BL::err('NameIsRequired'));
            $txtFromName->isFilled(BL::err('NameIsRequired'));
            $txtFromEmail->isFilled(BL::err('EmailIsRequired'));
            $txtReplyToEmail->isFilled(BL::err('EmailIsRequired'));

            // set form values
            $values = $this->frm->getValues();

            // check if at least one recipient group is chosen
            if (empty($values['groups'])) {
                $chkGroups->addError(BL::err('ChooseAtLeastOneGroup'));
            } else {
                // fetch the recipients for these groups
                $recipients = BackendMailmotorModel::getAddressesByGroupID($values['groups']);

                // if no recipients were found, throw an error
                if (empty($recipients)) {
                    $chkGroups->addError(BL::err('GroupsNoRecipients'));
                }
            }

            // check if at least one language is chosen
            if (empty($values['languages'])) {
                $rbtLanguages->isFilled(BL::err('FieldIsRequired'));
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // set values
                $item['name'] = $txtName->getValue();
                $item['from_name'] = $txtFromName->getValue();
                $item['from_email'] = $txtFromEmail->getValue();
                $item['reply_to_email'] = $txtReplyToEmail->getValue();
                $item['language'] = $rbtLanguages->getValue();
                $item['status'] = 'concept';
                $item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
                $item['edited_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
                if (!empty($values['campaign'])) {
                    $item['campaign_id'] = $this->frm->getField('campaign')->getValue();
                }

                // insert the concept
                $item['id'] = BackendMailmotorModel::insertMailing($item);

                // update the groups for this mailing
                BackendMailmotorModel::updateGroupsForMailing($item['id'], $values['groups']);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add_mailing_step1', array('item' => $item));

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('Edit') . '&id=' . $item['id'] . '&step=2');
            }
        }
    }
}
