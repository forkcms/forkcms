<?php

namespace Backend\Modules\FormBuilder\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

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
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendFormBuilderModel::exists($this->id)) {
            parent::execute();

            // get all data for the item we want to edit
            $this->record = (array) BackendFormBuilderModel::get($this->id);

            // delete item
            BackendFormBuilderModel::delete($this->id);

            // user was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
                rawurlencode($this->record['name'])
            );
        } else {
            // no item found, throw an exceptions, because somebody is fucking with our URL
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
