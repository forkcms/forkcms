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
 * This action will delete a blogpost
 *
 * @author Tijs Verkoyen <tijs@verkoyen.eu>
 */
class DeleteSpam extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        BackendBlogModel::deleteSpamComments();

        // item was deleted, so redirect
        $this->redirect(
            BackendModel::createURLForAction('Comments') .
            '&report=deleted-spam#tabSpam'
        );
    }
}
