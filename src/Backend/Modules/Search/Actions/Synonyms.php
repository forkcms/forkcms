<?php

namespace Backend\Modules\Search\Actions;

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
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the synonyms-action, it will display the overview of search synonyms
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Synonyms extends BackendBaseActionIndex
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
     * Loads the datagrids
     */
    private function loadDataGrid()
    {
        // create datagrid
        $this->dataGrid = new BackendDataGridDB(
            BackendSearchModel::QRY_DATAGRID_BROWSE_SYNONYMS,
            BL::getWorkingLanguage()
        );

        // sorting columns
        $this->dataGrid->setSortingColumns(array('term'), 'term');

        // column function
        $this->dataGrid->setColumnFunction('str_replace', array(',', ', ', '[synonym]'), 'synonym', true);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditSynonym')) {
            // set column URLs
            $this->dataGrid->setColumnURL('term', BackendModel::createURLForAction('EditSynonym') . '&amp;id=[id]');

            // add column
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('EditSynonym') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();

        // assign the datagrid
        $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
    }
}
