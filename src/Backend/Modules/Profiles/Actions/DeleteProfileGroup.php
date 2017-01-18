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

/**
 * This action will delete a membership of a profile in a group.
 */
class DeleteProfileGroup extends BackendBaseActionDelete
{
    /**
     * Execute the action.
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendProfilesModel::existsProfileGroup($this->id)) {
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
