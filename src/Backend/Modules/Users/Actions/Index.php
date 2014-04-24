<?php

namespace Backend\Modules\Users\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the index-action (default), it will display the users-overview
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
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
     * Load the datagrid.
     */
    private function loadDataGrid()
    {
        // create datagrid with an overview of all active and undeleted users
        $this->dataGrid = new BackendDataGridDB(BackendUsersModel::QRY_BROWSE, array('N'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // add column
            $this->dataGrid->addColumn(
                'nickname',
                \SpoonFilter::ucfirst(BL::lbl('Nickname')),
                null,
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );

            // add edit column
            if (BackendAuthentication::isAllowedAction('Add') || BackendAuthentication::getUser()->isGod()) {
                $this->dataGrid->addColumn(
                    'edit',
                    null,
                    BL::lbl('Edit'),
                    BackendModel::createURLForAction('Edit') . '&amp;id=[id]'
                );
            }
        }

        // show the user's nickname
        $this->dataGrid->setColumnFunction(
            array('Backend\\Modules\\Users\\Engine\\Model', 'getSetting'),
            array('[id]', 'nickname'),
            'nickname',
            false
        );
    }

    /**
     * Parse the datagrid
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
    }
}
