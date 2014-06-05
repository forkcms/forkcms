<?php

namespace Backend\Modules\Multisite\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Modules\Multisite\Engine\Model as MultisiteModel;
use Backend\Core\Engine\Model;

/**
 * Backend delete action for Multisite module.
 *
 * @author <per@wijs.be>
 */
class Delete extends ActionDelete
{
    public function execute()
    {
        parent::execute();
        $this->loadData();
        $this->delete();
    }

    private function delete()
    {
        MultisiteModel::delete($this->id);
        $this->redirect(
            Model::createURLForAction('Index') .
                '&report=deleted' .
                '&var=' . urlencode($this->record['domain'])
        );
    }

    private function loadData()
    {
        $this->id = $this->getParameter('id', 'int');
        if ($this->id === null || !MultisiteModel::exists($this->id)) {
            $this->redirect(
                Model::createURLForAction('Index') .
                '&error=non-existing'
            );
        }

        $this->record = MultisiteModel::get($this->id);
    }
}
