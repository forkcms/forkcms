<?php

namespace Backend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

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
        if ($this->id !== null && BackendFaqModel::existsCategory($this->id)) {
            $this->record = (array) BackendFaqModel::getCategory($this->id);

            if (BackendFaqModel::deleteCategoryAllowed($this->id)) {
                parent::execute();

                // delete item
                BackendFaqModel::deleteCategory($this->id);

                // category was deleted, so redirect
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&report=deleted-category&var=' .
                    rawurlencode($this->record['title'])
                );
            } else {
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&error=delete-category-not-allowed&var=' .
                    rawurlencode($this->record['title'])
                );
            }
        } else {
            $this->redirect(
                BackendModel::createURLForAction('Categories') . '&error=non-existing'
            );
        }
    }
}
