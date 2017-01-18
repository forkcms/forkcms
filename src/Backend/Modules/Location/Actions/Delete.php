<?php

namespace Backend\Modules\Location\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;

/**
 * This action will delete an item
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendLocationModel::exists($this->id)) {
            parent::execute();

            // get all data for the item we want to edit
            $this->record = (array) BackendLocationModel::get($this->id);

            // delete item
            BackendLocationModel::delete($this->id);

            // user was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
                rawurlencode($this->record['title'])
            );
        } else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
