<?php

namespace Backend\Modules\Profiles\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Mass action handler to delete profiles or add them to a specific group.
 */
class MassAction extends BackendBaseAction
{
    public function execute(): void
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        $this->checkToken();

        // action to execute
        $action = $this->getRequest()->query->get('action');
        if (!in_array($action, ['addToGroup', 'delete'])) {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=no-action-selected');
        }
        $ids = $this->getRequest()->query->has('id') ? (array) $this->getRequest()->query->get('id') : [];
        $newGroupId = $this->getRequest()->query->get('newGroup');

        // no ids provided
        if (empty($ids)) {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=no-profiles-selected');
        }

        // delete the given profiles
        if ($action === 'delete') {
            BackendProfilesModel::delete($ids);
            $report = 'deleted';
        } elseif ($action === 'addToGroup') {
            // add the profiles to the given group
            // no group id provided
            if (!array_key_exists($newGroupId, BackendProfilesModel::getGroups())) {
                $this->redirect(
                    BackendModel::createUrlForAction('Index') . '&error=no-group-selected'
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
                             'starts_on' => time(),
                        ]
                    );
                }
            }

            // report
            $report = 'added-to-group';
        } else {
            // unknown action
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=unknown-action');
        }

        // report
        $report = (count($ids) > 1 ? 'profiles-' : 'profile-') . $report;

        // redirect
        $this->redirect(
            BackendModel::createUrlForAction(
                'Index',
                null,
                null,
                [
                     'offset' => $this->getRequest()->query->get('offset', ''),
                     'order' => $this->getRequest()->query->get('order', ''),
                     'sort' => $this->getRequest()->query->get('sort', ''),
                     'email' => $this->getRequest()->query->get('email', ''),
                     'status' => $this->getRequest()->query->get('status', ''),
                     'group' => $this->getRequest()->query->get('group', ''),
                ]
            ) . '&report=' . $report
        );
    }
}
