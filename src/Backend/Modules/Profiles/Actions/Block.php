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
 * This action will toggle the block status a profile.
 */
class Block extends BackendBaseActionDelete
{
    /**
     * Execute the action.
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendProfilesModel::exists($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get item
            $profile = BackendProfilesModel::get($this->id);

            // already blocked? Prolly want to unblock then
            if ($profile['status'] === 'blocked') {
                // set profile status to active
                BackendProfilesModel::update($this->id, array('status' => 'active'));

                // redirect
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=profile-unblocked&var=' . rawurlencode(
                        $profile['email']
                    ) . '&highlight=row-' . $this->id
                );
            } else {
                // delete profile session that may be active
                BackendProfilesModel::deleteSession($this->id);

                // set profile status to blocked
                BackendProfilesModel::update($this->id, array('status' => 'blocked'));

                // redirect
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=profile-blocked&var=' . rawurlencode(
                        $profile['email']
                    ) . '&highlight=row-' . $this->id
                );
            }
        } else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
