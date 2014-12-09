<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\ContentBlocks\Engine\Model as BackendContentBlocksModel;

/**
 * This is the delete-action, it will delete an item.
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
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

        $this->record = BackendContentBlocksModel::get($this->id);

        // does the item exist
        if ($this->id !== null && !empty($this->record)) {
            parent::execute();

            // delete item
            BackendContentBlocksModel::delete($this->record);

            // trigger event
            BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

            // item was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
                urlencode($this->record->getTitle())
            );
        } else {
            // no item found, redirect to the overview with an error
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
