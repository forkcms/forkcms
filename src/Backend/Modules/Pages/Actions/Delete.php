<?php

namespace Backend\Modules\Pages\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the delete-action, it will delete a page
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
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
        if ($this->id === 0 || !BackendPagesModel::exists($this->id)) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }

        parent::execute();

        // cannot have children
        if (BackendPagesModel::getFirstChildId($this->id) !== false) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }

        $revisionId = $this->getRequest()->query->getInt('revision_id');
        if ($revisionId === 0) {
            $revisionId = null;
        }

        $page = BackendPagesModel::get($this->id, $revisionId);

        if (empty($page)) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }

        $success = BackendPagesModel::delete($this->id, null, $revisionId);

        // delete search indexes
        BackendSearchModel::removeIndex($this->getModule(), $this->id);

        // build cache
        BackendPagesModel::buildCache(BL::getWorkingLanguage());

        if (!$success) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }

        $this->redirect(BackendModel::createUrlForAction(
            'Index',
            null,
            null,
            ['id' => $page['parent_id'], 'report' => 'deleted', 'var' => $page['title']]
        ));
    }
}
