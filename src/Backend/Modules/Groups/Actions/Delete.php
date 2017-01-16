<?php

namespace Backend\Modules\Groups\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

/**
 * This is the delete-action, it will delete an item.
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // group exists and id is not null?
        if ($this->id !== null && BackendGroupsModel::exists($this->id)) {
            parent::execute();

            // get record
            $this->record = BackendGroupsModel::get($this->id);

            // delete group
            BackendGroupsModel::delete($this->id);

            // item was deleted, so redirect
            $this->redirect(BackendModel::createURLForAction('Index') . '&report=deleted&var=' . rawurlencode($this->record['name']));
        } else {
            // no item found, redirect to the overview with an error
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
