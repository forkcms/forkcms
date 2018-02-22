<?php

namespace App\Backend\Modules\Users\Actions;

use App\Backend\Core\Engine\Base\Action as BackendBaseAction;
use App\Backend\Core\Engine\Model as BackendModel;
use App\Backend\Core\Engine\User as BackendUser;
use App\Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the undo-delete-action, it will restore a deleted user
 */
class UndoDelete extends BackendBaseAction
{
    public function execute(): void
    {
        $email = $this->getRequest()->query->get('email', '');

        // does the user exist
        if ($email !== '') {
            parent::execute();

            // delete item
            if (BackendUsersModel::undoDelete($email)) {
                // get user
                $user = new BackendUser(null, $email);

                // item was deleted, so redirect
                $this->redirect(
                    BackendModel::createUrlForAction('edit') . '&id=' . $user->getUserId(
                    ) . '&report=restored&var=' . $user->getSetting('nickname') . '&highlight=row-' . $user->getUserId()
                );
            } else {
                // invalid user
                $this->redirect(BackendModel::createUrlForAction('index') . '&error=non-existing');
            }
        } else {
            $this->redirect(BackendModel::createUrlForAction('index') . '&error=non-existing');
        }
    }
}
