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
use Backend\Modules\Profiles\Form\GroupDeleteType;

/**
 * This action will delete a profile group.
 */
class DeleteGroup extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(GroupDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        // get parameters
        $this->id = (int) $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendProfilesModel::existsGroup($this->id)) {
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=non-existing');
        }

        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get group
        $group = BackendProfilesModel::getGroup($this->id);

        // delete group
        BackendProfilesModel::deleteGroup($this->id);

        // group was deleted, so redirect
        $this->redirect(
            BackendModel::createURLForAction('Groups') . '&report=deleted&var=' . rawurlencode($group['name'])
        );
    }
}
