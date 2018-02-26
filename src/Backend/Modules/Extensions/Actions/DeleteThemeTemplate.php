<?php

namespace ForkCMS\Backend\Modules\Extensions\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use ForkCMS\Backend\Core\Engine\Model as BackendModel;
use ForkCMS\Backend\Form\Type\DeleteType;
use ForkCMS\Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

/**
 * This is the delete-action, it will delete a template
 */
class DeleteThemeTemplate extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            null,
            ['module' => $this->getModule(), 'action' => 'DeleteThemeTemplate']
        );
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createUrlForAction(
                'ThemeTemplates',
                null,
                null,
                ['error' => 'something-went-wrong']
            ));

            return;
        }
        $deleteFormData = $deleteForm->getData();

        $this->id = (int) $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendExtensionsModel::existsTemplate($this->id)) {
            $this->redirect(BackendModel::createUrlForAction(
                'ThemeTemplates',
                null,
                null,
                ['error' => 'non-existing']
            ));

            return;
        }

        parent::execute();

        $success = false;
        $item = BackendExtensionsModel::getTemplate($this->id);
        if (!empty($item)) {
            $success = BackendExtensionsModel::deleteTemplate($this->id);
        }

        if (!$success) {
            $this->redirect(BackendModel::createUrlForAction(
                'ThemeTemplates',
                null,
                null,
                ['error' => 'non-existing']
            ));

            return;
        }

        $this->redirect(BackendModel::createUrlForAction(
            'ThemeTemplates',
            null,
            null,
            ['theme' => $item['theme'], 'report' => 'deleted-template', 'var' => $item['label']]
        ));
    }
}
