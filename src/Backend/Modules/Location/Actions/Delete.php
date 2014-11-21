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
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Mathias Dewelde <mathias@studiorauw.be>
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

        // get the location
        $this->record = BackendLocationModel::get($this->id);

        // does the item exist
        if ($this->id !== null || !empty($this->record)) {
            parent::execute();

            // delete item
            BackendLocationModel::delete($this->record);

            // trigger event
            BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

            // user was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
                urlencode($this->record->getTitle())
            );
        }

        // something went wrong
        else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
