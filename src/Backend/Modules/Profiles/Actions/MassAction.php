<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;

/**
 * Mass action handler to delete profiles or add them to a specific group.
 */
class MassAction extends BackendBaseAction
{
    public function execute(): void
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // action to execute
        $action = $this->getRequest()->query->get('action');
        if (!in_array($action, ['addToGroup', 'delete'])) {
            $action = '';
        }
        $ids = $this->getRequest()->query->has('id') ? (array) $this->getRequest()->query->get('id') : [];
        $newGroupId = $this->getRequest()->query->get('newGroup');
        if (!array_key_exists($newGroupId, BackendProfilesModel::getGroups())) {
            $newGroupId = '';
        }

        // no ids provided
        if (empty($ids)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=no-profiles-selected');
        }

        // delete the given profiles
        if ($action === 'delete') {
            BackendProfilesModel::delete($ids);
            $report = 'deleted';
        } elseif ($action === 'addToGroup') {
            // add the profiles to the given group
            // no group id provided
            if ($newGroupId == '') {
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&error=no-group-selected'
                );
            }

            // set new status
            foreach ($ids as $id) {
                // profile must exist
                if (BackendProfilesModel::exists($id)) {
                    // make sure the user is not already part of this group without an expiration date
                    foreach (BackendProfilesModel::getProfileGroups($id) as $existingGroup) {
                        // if he is, skip to the next user
                        if ($existingGroup['group_id'] === $newGroupId) {
                            continue 2;
                        }
                    }

                    // OK, it's safe to add the user to this group
                    BackendProfilesModel::insertProfileGroup(
                        [
                             'profile_id' => $id,
                             'group_id' => $newGroupId,
                             'starts_on' => BackendModel::getUTCDate(),
                        ]
                    );
                }
            }

            // report
            $report = 'added-to-group';
        } else {
            // unknown action
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=unknown-action');
        }

        // report
        $report = (count($ids) > 1 ? 'profiles-' : 'profile-') . $report;

        // redirect
        $this->redirect(
            BackendModel::createURLForAction(
                'Index',
                null,
                null,
                [
                     'offset' => $this->getRequest()->get('offset', ''),
                     'order' => $this->getRequest()->get('order', ''),
                     'sort' => $this->getRequest()->get('sort', ''),
                     'email' => $this->getRequest()->get('email', ''),
                     'status' => $this->getRequest()->get('status', ''),
                     'group' => $this->getRequest()->get('group', ''),
                ]
            ) . '&report=' . $report
        );
    }
}
