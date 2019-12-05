<?php

namespace Backend\Modules\FormBuilder\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This action will delete a category
 */
class DeleteEmail extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            null,
            ['module' => $this->getModule(), 'action' => 'DeleteEmail']
        );
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createUrlForAction(
                'Emails',
                null,
                null,
                ['error' => 'something-went-wrong']
            ));

            return;
        }
        $deleteFormData = $deleteForm->getData();

        $this->id = (int) $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendFormBuilderModel::existsEmail($this->id)) {
            $this->redirect(BackendModel::createUrlForAction('Emails', null, null, ['error' => 'non-existing']));

            return;
        }

        $this->record = (array) BackendFormBuilderModel::getEmail($this->id);

        parent::execute();

        BackendFormBuilderModel::deleteEmail($this->id);

        $this->redirect(BackendModel::createUrlForAction(
            'Emails',
            null,
            null,
            ['report' => 'deleted-email', 'id' => $this->record['form_id']]
        ));
    }
}
