<?php

namespace Backend\Modules\Search\Actions;

use Backend\Core\Engine\Base\Action;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use App\Component\Locale\BackendLanguage;
use App\Component\Model\BackendModel;
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
        $dataGrid = new BackendDataGridDatabase(BackendSearchModel::QUERY_DATAGRID_BROWSE_SYNONYMS, [BackendLanguage::getWorkingLanguage()]);
        $dataGrid->setSortingColumns(['term'], 'term');
        $dataGrid->setColumnFunction('str_replace', [',', ', ', '[synonym]'], 'synonym', true);

        if (BackendAuthentication::isAllowedAction('EditSynonym')) {
            $editUrl = BackendModel::createUrlForAction('EditSynonym') . '&amp;id=[id]';
            $dataGrid->setColumnURL('term', $editUrl);
            $dataGrid->addColumn('edit', null, BackendLanguage::lbl('Edit'), $editUrl, BackendLanguage::lbl('Edit'));
        }

        $this->template->assign('dataGrid', $dataGrid->getContent());
    }
}
