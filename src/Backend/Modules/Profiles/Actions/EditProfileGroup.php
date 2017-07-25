<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;

/**
 * This is the edit_profile_group-action, it will display a form to add a profile to a group.
 */
class EditProfileGroup extends BackendBaseActionEdit
{
    /**
     * Info about a group membership.
     *
     * @var array
     */
    private $profileGroup;

    /**
     * @var int
     */
    private $profileId;

    public function execute(): void
    {
        // get parameters
        $this->id = $this->getRequest()->query->getInt('id');
        $this->profileId = $this->getRequest()->query->getInt('profile_id');

        // does the item exists
        if ($this->id !== 0 && BackendProfilesModel::existsProfileGroup($this->id)) {
            // does profile exists
            if ($this->profileId !== 0 && BackendProfilesModel::exists($this->profileId)) {
                parent::execute();
                $this->getData();
                $this->loadForm();
                $this->validateForm();
                $this->loadDeleteForm();
                $this->parse();
                $this->display();
            } else {
                $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
            }
        } else {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->profileGroup = BackendProfilesModel::getProfileGroup($this->id);
    }

    private function loadForm(): void
    {
        // get group values for dropdown
        $ddmValues = BackendProfilesModel::getGroupsForDropDown($this->profileId, $this->id);

        // create form
        $this->form = new BackendForm('editProfileGroup');

        // create elements
        $this->form->addDropdown('group', $ddmValues, $this->profileGroup['group_id']);
        $this->form->addDate('expiration_date', $this->profileGroup['expires_on']);
        $this->form->addTime(
            'expiration_time',
            ($this->profileGroup['expires_on'] !== null) ? date('H:i', $this->profileGroup['expires_on']) : ''
        );

        // set default element
        $this->form->getField('group')->setDefaultElement('');
    }

    protected function parse(): void
    {
        parent::parse();

        // assign the active record and additional variables
        $this->template->assign('profileGroup', $this->profileGroup);
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // get fields
            $ddmGroup = $this->form->getField('group');
            $txtExpirationDate = $this->form->getField('expiration_date');
            $txtExpirationTime = $this->form->getField('expiration_time');

            // fields filled?
            $ddmGroup->isFilled(BL::getError('GroupIsRequired'));
            if ($txtExpirationDate->isFilled()) {
                $txtExpirationDate->isValid(BL::getError('DateIsInvalid'));
            }
            if ($txtExpirationTime->isFilled()) {
                $txtExpirationTime->isValid(BL::getError('TimeIsInvalid'));
            }

            // no errors?
            if ($this->form->isCorrect()) {
                // build item
                $values = ['group_id' => $ddmGroup->getSelected()];

                // only format date if not empty
                if ($txtExpirationDate->isFilled() && $txtExpirationTime->isFilled()) {
                    // format date
                    $values['expires_on'] = BackendModel::getUTCDate(
                        null,
                        BackendModel::getUTCTimestamp($txtExpirationDate, $txtExpirationTime)
                    );
                } else {
                    // reset expiration date
                    $values['expires_on'] = null;
                }

                // update values
                BackendProfilesModel::updateProfileGroup($this->id, $values);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createUrlForAction(
                        'Edit'
                    ) . '&id=' . $this->profileId . '&report=membership-saved&var=' . rawurlencode(
                        $values['group_id']
                    ) . '&highlight=row-' . $this->id . '#tabGroups'
                );
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->profileGroup['id']],
            ['module' => $this->getModule(), 'action' => 'DeleteProfileGroup']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
