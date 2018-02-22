<?php

namespace App\Backend\Modules\Groups\Actions;

use App\Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use App\Backend\Core\Engine\Authentication as BackendAuthentication;
use App\Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use App\Backend\Core\Language\Language as BL;
use App\Backend\Core\Engine\Model as BackendModel;
use App\Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

/**
 * This is the index-action (default), it will display the groups-overview
 */
class Index extends BackendBaseActionIndex
{
    public function execute(): void
    {
        parent::execute();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    public function loadDataGrid(): void
    {
        $this->dataGrid = new BackendDataGridDatabase(BackendGroupsModel::QUERY_BROWSE);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL('name', BackendModel::createUrlForAction('Edit') . '&amp;id=[id]');
            $this->dataGrid->setColumnURL('num_users', BackendModel::createUrlForAction('Edit') . '&amp;id=[id]#tabUsers');
            $this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createUrlForAction('Edit') . '&amp;id=[id]');
        }
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('dataGrid', $this->dataGrid->getContent());
    }
}
