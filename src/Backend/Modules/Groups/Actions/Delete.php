<?php

namespace Backend\Modules\Groups\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;
use Backend\Modules\Groups\Form\GroupDeleteType;

/**
 * This is the delete-action, it will delete an item.
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(GroupDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Index', null, null, ['error' => 'something-went-wrong']));

            return;
        }
        $deleteFormData = $deleteForm->getData();

        $this->id = $deleteFormData['id'];

        // group exists and id is not null?
        if ($this->id === 0 || !BackendGroupsModel::exists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }

        parent::execute();

        $this->record = BackendGroupsModel::get($this->id);

        BackendGroupsModel::delete($this->id);

        $this->redirect(BackendModel::createURLForAction(
            'Index',
            null,
            null,
            ['report' => 'deleted', 'var' => $this->record['name']]
        ));
    }
}
