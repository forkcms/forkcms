<?php

namespace Backend\Modules\Location\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;
use Backend\Modules\Location\Form\LocationDeleteType;

/**
 * This action will delete an item
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(LocationDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        // get parameters
        $this->id = $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendLocationModel::exists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        parent::execute();

        // get all data for the item we want to edit
        $this->record = (array) BackendLocationModel::get($this->id);

        // delete item
        BackendLocationModel::delete($this->id);

        // user was deleted, so redirect
        $this->redirect(
            BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
            rawurlencode($this->record['title'])
        );
    }
}
