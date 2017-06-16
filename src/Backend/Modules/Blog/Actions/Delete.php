<?php

namespace Backend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;
use Backend\Modules\Blog\Form\BlogDeleteType;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This action will delete a blogpost
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(BlogDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        // get parameters
        $this->id = (int) $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendBlogModel::exists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // set category id
        $categoryId = (int) $deleteFormData['categoryId'];

        // get data
        $this->record = (array) BackendBlogModel::get($this->id);

        // delete item
        BackendBlogModel::delete($this->id);

        // delete search indexes
        BackendSearchModel::removeIndex($this->getModule(), $this->id);

        // build redirect URL
        $redirectUrl = BackendModel::createURLForAction('Index') . '&report=deleted&var=' . rawurlencode($this->record['title']);

        // append to redirect URL
        if ($categoryId !== 0) {
            $redirectUrl .= '&category=' . $categoryId;
        }

        // item was deleted, so redirect
        $this->redirect($redirectUrl);
    }
}
