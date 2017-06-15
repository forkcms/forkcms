<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;
use Backend\Modules\Profiles\Form\ProfileGroupDeleteType;

/**
 * This action will delete a membership of a profile in a group.
 */
class DeleteProfileGroup extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(ProfileGroupDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        // get parameters
        $this->id = (int) $deleteFormData['id'];

        // does the item exist
        if ($this->id !== 0 && BackendProfilesModel::existsProfileGroup($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get profile group
            $profileGroup = BackendProfilesModel::getProfileGroup($this->id);

            // delete profile group
            BackendProfilesModel::deleteProfileGroup($this->id);

            // profile group was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction(
                    'edit'
                ) . '&id=' . $profileGroup['profile_id'] . '&report=membership-deleted#tabGroups'
            );
        } else {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }
    }
}
