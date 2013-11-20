<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add_profile_group-action, it will display a form to add a profile to a group.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendProfilesAddProfileGroup extends BackendBaseActionEdit
{
    /**
     * Execute the action.
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if($this->id !== null && BackendProfilesModel::exists($this->id)) {
            parent::execute();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        }

        // no item found, redirect to index, because somebody is fucking with our URL
        else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
    }

    /**
     * Load the form.
     */
    private function loadForm()
    {
        // get group values for dropdown
        $ddmValues = BackendProfilesModel::getGroupsForDropDown($this->id);

        // create form
        $this->frm = new BackendForm('addProfileGroup');

        // create elements
        $this->frm->addDropdown('group', $ddmValues);
        $this->frm->addDate('expiration_date');
        $this->frm->addTime('expiration_time', '');

        // set default element
        $this->frm->getField('group')->setDefaultElement('');
    }

    /**
     * Validate the form.
     */
    private function validateForm()
    {
        // is the form submitted?
        if($this->frm->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // get fields
            $ddmGroup = $this->frm->getField('group');
            $txtExpirationDate = $this->frm->getField('expiration_date');
            $txtExpirationTime = $this->frm->getField('expiration_time');

            // fields filled?
            $ddmGroup->isFilled(BL::getError('FieldIsRequired'));
            if($txtExpirationDate->isFilled()) $txtExpirationDate->isValid(BL::getError('DateIsInvalid'));
            if($txtExpirationTime->isFilled()) $txtExpirationTime->isValid(BL::getError('TimeIsInvalid'));

            // no errors?
            if($this->frm->isCorrect()) {
                // build item
                $values['profile_id'] = $this->id;
                $values['group_id'] = $ddmGroup->getSelected();
                $values['starts_on'] = BackendModel::getUTCDate();

                // only format date if not empty
                if($txtExpirationDate->isFilled() && $txtExpirationTime->isFilled()) {
                    // format date
                    $values['expires_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($txtExpirationDate, $txtExpirationTime));
                }

                // insert values
                $id = BackendProfilesModel::insertProfileGroup($values);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_profile_add_to_group', array('item' => $values));

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $values['profile_id'] . '&report=membership-added&highlight=row-' . $id . '#tabGroups');
            }
        }
    }
}
