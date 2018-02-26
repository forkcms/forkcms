<?php

namespace ForkCMS\Backend\Modules\Blog\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use ForkCMS\Backend\Core\Engine\Model as BackendModel;
use ForkCMS\Backend\Modules\Blog\Engine\Model as BackendBlogModel;
use ForkCMS\Backend\Modules\Blog\Form\BlogDeleteType;
use ForkCMS\Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This action will delete a blogpost
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(
            BlogDeleteType::class,
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
        if ($this->id === 0 || !BackendBlogModel::exists($this->id)) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }

        parent::execute();

        $categoryId = (int) $deleteFormData['categoryId'];

        $this->record = (array) BackendBlogModel::get($this->id);

        BackendBlogModel::delete($this->id);

        // delete search indexes
        BackendSearchModel::removeIndex($this->getModule(), $this->id);

        $redirectParameters = ['report' => 'deleted', 'var' => $this->record['title']];
        if ($categoryId !== 0) {
            $redirectParameters['category'] = $categoryId;
        }

        $this->redirect(BackendModel::createUrlForAction(
            'Index',
            null,
            null,
            $redirectParameters
        ));
    }
}
