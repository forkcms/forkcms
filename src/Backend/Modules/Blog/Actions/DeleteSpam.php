<?php

namespace Backend\Modules\Blog\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use App\Component\Model\BackendModel;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

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
