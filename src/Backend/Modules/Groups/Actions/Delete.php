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
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@gmail.com>
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        parent::execute();
        $this->record = BackendGroupsModel::get($this->id);

        if ($this->record !== null) {
            // delete item
            BackendGroupsModel::delete($this->record);
            BackendModel::triggerEvent(
                $this->getModule(),
                'after_delete',
                array('item' => $this->record)
            );

            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
                urlencode($this->record->getName())
            );
        } else {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&error=non-existing'
            );
        }
    }
}
