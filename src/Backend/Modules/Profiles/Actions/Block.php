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
    public function execute(): void
    {
        // get parameters
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendProfilesModel::exists($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get item
            $profile = BackendProfilesModel::get($this->id);

            // already blocked? Prolly want to unblock then
            if ($profile['status'] === 'blocked') {
                // set profile status to active
                BackendProfilesModel::update($this->id, ['status' => 'active']);

                // redirect
                $this->redirect(
                    BackendModel::createUrlForAction('Index') . '&report=profile-unblocked&var=' . rawurlencode(
                        $profile['email']
                    ) . '&highlight=row-' . $this->id
                );
            } else {
                // delete profile session that may be active
                BackendProfilesModel::deleteSession($this->id);

                // set profile status to blocked
                BackendProfilesModel::update($this->id, ['status' => 'blocked']);

                // redirect
                $this->redirect(
                    BackendModel::createUrlForAction('Index') . '&report=profile-blocked&var=' . rawurlencode(
                        $profile['email']
                    ) . '&highlight=row-' . $this->id
                );
            }
        } else {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }
}
