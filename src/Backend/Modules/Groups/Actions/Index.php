<?php

namespace Backend\Modules\Groups\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

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
