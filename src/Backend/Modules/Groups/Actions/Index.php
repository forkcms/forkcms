<?php

namespace Backend\Modules\Groups\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DatagridDB as BackendDataGridDB;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

/**
 * This is the index-action (default), it will display the groups-overview
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 */
class Index extends BackendBaseActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    /**
     * Load the datagrid
     */
    public function loadDataGrid()
    {
        $this->dataGrid = new BackendDataGridDB(BackendGroupsModel::QRY_BROWSE);

        // check if this action is allowed
        if(BackendAuthentication::isAllowedAction('edit')) {
            $this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
            $this->dataGrid->setColumnURL('num_users', BackendModel::createURLForAction('edit') . '&amp;id=[id]#tabUsers');
            $this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]');
        }
    }

    /**
     * Parse the datagrid
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('dataGrid', $this->dataGrid->getContent());
    }
}
