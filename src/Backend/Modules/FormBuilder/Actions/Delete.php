<?php

namespace Backend\Modules\FormBuilder\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;
use Backend\Modules\FormBuilder\Form\FormBuilderDeleteType;

/**
 * This is the delete-action, it will delete an item.
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(FormBuilderDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        // get parameters
        $this->id = $deleteFormData['id'];

        // does the item exist
        if ($this->id !== 0 && BackendFormBuilderModel::exists($this->id)) {
            parent::execute();

            // get all data for the item we want to edit
            $this->record = (array) BackendFormBuilderModel::get($this->id);

            // delete item
            BackendFormBuilderModel::delete($this->id);

            // user was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
                rawurlencode($this->record['name'])
            );
        } else {
            // no item found, throw an exceptions, because somebody is fucking with our URL
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
