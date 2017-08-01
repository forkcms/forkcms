<?php

namespace Backend\Modules\Extensions\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

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
