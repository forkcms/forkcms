<?php

namespace Backend\Modules\Search\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the synonyms-action, it will display the overview of search synonyms
 */
class Synonyms extends Action
{
    public function execute(): void
    {
        parent::execute();
        $this->showDataGrid();
        $this->display();
    }

    private function showDataGrid(): void
    {
        $dataGrid = new BackendDataGridDatabase(BackendSearchModel::QUERY_DATAGRID_BROWSE_SYNONYMS, [BL::getWorkingLanguage()]);
        $dataGrid->setSortingColumns(['term'], 'term');
        $dataGrid->setColumnFunction('str_replace', [',', ', ', '[synonym]'], 'synonym', true);

        if (BackendAuthentication::isAllowedAction('EditSynonym')) {
            $editUrl = BackendModel::createUrlForAction('EditSynonym') . '&amp;id=[id]';
            $dataGrid->setColumnURL('term', $editUrl);
            $dataGrid->addColumn('edit', null, BL::lbl('Edit'), $editUrl, BL::lbl('Edit'));
        }

        $this->template->assign('dataGrid', $dataGrid->getContent());
    }
}
