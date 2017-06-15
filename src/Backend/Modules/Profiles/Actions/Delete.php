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
use Backend\Modules\Profiles\Form\ProfileDeleteType;

/**
 * This action will delete or restore a profile.
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(ProfileDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        // get parameters
        $this->id = (int) $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendProfilesModel::exists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get profile
        $profile = BackendProfilesModel::get($this->id);

        // already deleted? Prolly want to undo then
        if ($profile['status'] === 'deleted') {
            // set profile status to active
            BackendProfilesModel::update($this->id, ['status' => 'active']);

            // redirect
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=profile-undeleted&var=' . rawurlencode(
                    $profile['email']
                ) . '&highlight=row-' . $profile['id']
            );
        } else {
            // delete profile
            BackendProfilesModel::delete($this->id);

            // redirect
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=profile-deleted&var=' . rawurlencode(
                    $profile['email']
                ) . '&highlight=row-' . $profile['id']
            );
        }
    }
}
