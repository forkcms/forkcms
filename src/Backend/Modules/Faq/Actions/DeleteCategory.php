<?php

namespace Backend\Modules\Faq\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * This action will delete a category
 */
class DeleteCategory extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            null,
            ['module' => $this->getModule(), 'action' => 'DeleteCategory']
        );
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createUrlForAction(
                'Categories',
                null,
                null,
                ['error' => 'something-went-wrong']
            ));

            return;
        }
        $deleteFormData = $deleteForm->getData();

        $this->id = $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendFaqModel::existsCategory($this->id)) {
            $this->redirect(BackendModel::createUrlForAction('Categories', null, null, ['error' => 'non-existing']));

            return;
        }

        $this->record = BackendFaqModel::getCategory($this->id);

        if (!BackendFaqModel::deleteCategoryAllowed($this->id)) {
            $this->redirect(BackendModel::createUrlForAction(
                'Categories',
                null,
                null,
                ['error' => 'delete-category-not-allowed', 'var' => $this->record['title']]
            ));

            return;
        }

        parent::execute();

        BackendFaqModel::deleteCategory($this->id);

        $this->redirect(BackendModel::createUrlForAction(
            'Categories',
            null,
            null,
            ['report' => 'deleted-category', 'var' => $this->record['title']]
        ));
    }
}
