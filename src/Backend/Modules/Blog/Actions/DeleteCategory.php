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

/**
 * This action will delete a category
 */
class DeleteCategory extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendBlogModel::existsCategory($this->id)) {
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
        } else {
            // something went wrong
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=non-existing');
        }
    }
}
