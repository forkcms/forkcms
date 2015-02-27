<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;

/**
 * This page will display the overview of groups
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Groups extends BackendBaseActionIndex
{
    const PAGING_LIMIT = 10;

    /**
     * Checks if default groups were set, and shows a message with more info if they are not.
     */
    private function checkForDefaultGroups()
    {
        // groups are already set
        if (BackendModel::getModuleSetting($this->getModule(), 'cm_groups_defaults_set')) {
            return true;
        }

        // show the message
        $this->tpl->assign('noDefaultsSet', true);
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->checkForDefaultGroups();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    /**
     * Loads the datagrid with the groups
     */
    private function loadDataGrid()
    {
        // create datagrid
        $this->dataGrid = new BackendDataGridDB(BackendMailmotorModel::QRY_DATAGRID_BROWSE_GROUPS);
        $this->dataGrid->setColumnsHidden(array('language', 'is_default'));

        // sorting columns
        $this->dataGrid->setSortingColumns(array('name', 'created_on'), 'created_on');
        $this->dataGrid->setSortParameter('desc');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Addresses')) {
            // set column URLs
            $this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('Addresses') . '&amp;group_id=[id]');
        }

        // set the datagrid ID so we don't run into trouble with multiple datagrids that use mass actions
        $this->dataGrid->setAttributes(array('id' => 'dgGroups'));

        // add the multicheckbox column
        $this->dataGrid->setMassActionCheckboxes('checkbox', '[id]', BackendMailmotorModel::getDefaultGroupIds());
        $this->dataGrid->setColumnsSequence('checkbox', 'name', 'created_on', 'language');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
        $this->dataGrid->setMassAction($ddmMassAction);

        // set column functions
        $this->dataGrid->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            array('[created_on]'),
            'created_on',
            true
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('CustomFields')) {
            $this->dataGrid->addColumnAction(
                'custom_fields',
                null,
                BL::lbl('CustomFields'),
                BackendModel::createURLForAction('CustomFields') . '&amp;group_id=[id]',
                BL::lbl('CustomFields'),
                array('class' => 'button icon iconEdit linkButton')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('ExportAddresses')) {
            $this->dataGrid->addColumnAction(
                'export',
                null,
                BL::lbl('Export'),
                BackendModel::createURLForAction('ExportAddresses') . '&amp;id=[id]',
                BL::lbl('Export'),
                array('class' => 'button icon iconExport linkButton')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditGroup')) {
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('EditGroup') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }

        // add styles
        $this->dataGrid->setColumnAttributes('name', array('class' => 'title'));

        // set paging limit
        $this->dataGrid->setPagingLimit(static::PAGING_LIMIT);
    }

    /**
     * Parse all datagrids
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
    }
}
