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
 * This action will delete a question
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        if ($this->id !== null && BackendFaqModel::exists($this->id)) {
            parent::execute();
            $this->record = BackendFaqModel::get($this->id);

            // delete item
            BackendFaqModel::delete($this->id);
            BackendModel::triggerEvent(
                $this->getModule(),
                'after_delete',
                array('item' => $this->record)
            );

            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
                urlencode($this->record['question'])
            );
        } else {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&error=non-existing'
            );
        }
    }
}
