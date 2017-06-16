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
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        if ($this->id !== 0 && BackendFaqModel::exists($this->id)) {
            parent::execute();
            $this->record = BackendFaqModel::get($this->id);

            // delete item
            BackendFaqModel::delete($this->id);

            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=deleted&var=' .
                rawurlencode($this->record['question'])
            );
        } else {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&error=non-existing'
            );
        }
    }
}
