<?php

namespace Backend\Modules\Users\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the undo-delete-action, it will restore a deleted user
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendUsersUndoDelete extends BackendBaseAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $email = $this->getParameter('email', 'string');

        // does the user exist
        if ($email !== null) {
            parent::execute();

            // delete item
            if (BackendUsersModel::undoDelete($email)) {
                // get user
                $user = new BackendUser(null, $email);

                // item was deleted, so redirect
                $this->redirect(
                    BackendModel::createURLForAction('edit') . '&id=' . $user->getUserId(
                    ) . '&report=restored&var=' . $user->getSetting('nickname') . '&highlight=row-' . $user->getUserId()
                );
            } else {
                // invalid user
                $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
            }
        } else {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }
    }
}
