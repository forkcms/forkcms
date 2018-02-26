<?php

namespace ForkCMS\Backend\Modules\Blog\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use ForkCMS\Backend\Core\Engine\Model as BackendModel;
use ForkCMS\Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This action will delete a blogpost
 */
class DeleteSpam extends BackendBaseActionDelete
{
    public function execute(): void
    {
        parent::execute();
        BackendBlogModel::deleteSpamComments();

        // item was deleted, so redirect
        $this->redirect(
            BackendModel::createUrlForAction('Comments') .
            '&report=deleted-spam#tabSpam'
        );
    }
}
