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
use Backend\Form\Type\DeleteType;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;

/**
 * This action will delete or restore a profile.
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            null,
            ['module' => $this->getModule()]
        );
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'something-went-wrong']));

            return;
        }
        $deleteFormData = $deleteForm->getData();

        $this->id = (int) $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendProfilesModel::exists($this->id)) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }

        parent::execute();

        $profile = BackendProfilesModel::get($this->id);

        // already deleted? Prolly want to undo then
        if ($profile['status'] === 'deleted') {
            // set profile status to active
            BackendProfilesModel::update($this->id, ['status' => 'active']);

            $this->redirect(BackendModel::createUrlForAction(
                'Index',
                null,
                null,
                ['report' => 'profile-undeleted', 'var' => $profile['email'], 'highlight=row-' . $profile['id']]
            ));

            return;
        }

        BackendProfilesModel::delete($this->id);

        $this->redirect(BackendModel::createUrlForAction(
            'Index',
            null,
            null,
            ['report' => 'profile-deleted', 'var' => $profile['email'], 'highlight=row-' . $profile['id']]
        ));
    }
}
