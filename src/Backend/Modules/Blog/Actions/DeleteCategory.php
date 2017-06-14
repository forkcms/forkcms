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
use Backend\Modules\Blog\Form\BlogCategoryDeleteType;

/**
 * This action will delete a category
 */
class DeleteCategory extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(BlogCategoryDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        $this->id = (int) $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendBlogModel::existsCategory($this->id)) {
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=non-existing');
        }

        // get data
        $this->record = (array) BackendBlogModel::getCategory($this->id);

        // allowed to delete the category?
        if (BackendBlogModel::deleteCategoryAllowed($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // delete item
            BackendBlogModel::deleteCategory($this->id);

            // category was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction('Categories') . '&report=deleted-category&var=' .
                rawurlencode($this->record['title'])
            );
        } else {
            $this->redirect(
                // delete category not allowed
                BackendModel::createURLForAction('Categories') . '&error=delete-category-not-allowed&var=' .
                rawurlencode($this->record['title'])
            );
        }
    }
}
