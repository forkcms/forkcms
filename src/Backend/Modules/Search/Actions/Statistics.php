<?php

namespace Backend\Modules\Search\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the statistics-action, it will display the overview of search statistics
 */
class Statistics extends Action
{
    public function execute()
    {
        parent::execute();
        $this->showDataGrid();
        $this->display();
    }

    private function showDataGrid()
    {
        $dataGrid = new BackendDataGridDB(
            BackendSearchModel::QRY_DATAGRID_BROWSE_STATISTICS,
            [BL::getWorkingLanguage()]
        );
        $dataGrid->setColumnsHidden(['data']);
        $dataGrid->addColumn('referrer', BL::lbl('Referrer'));
        $dataGrid->setHeaderLabels(['time' => \SpoonFilter::ucfirst(BL::lbl('SearchedOn'))]);

        // set column function
        $dataGrid->setColumnFunction([__CLASS__, 'setReferrer'], '[data]', 'referrer');
        $dataGrid->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[time]'],
            'time',
            true
        );
        $dataGrid->setColumnFunction('htmlspecialchars', ['[term]'], 'term');

        $dataGrid->setSortingColumns(['time', 'term'], 'time');
        $dataGrid->setSortParameter('desc');

        $this->tpl->assign('dataGrid', $dataGrid->getContent());
    }

    /**
     * @param string $data The source data.
     *
     * @return string
     */
    public static function setReferrer(string $data): string
    {
        $data = unserialize($data);
        if (!isset($data['server']['HTTP_REFERER'])) {
            return '';
        }

        $referrer = htmlspecialchars($data['server']['HTTP_REFERER']);

        return '<a href="' . $referrer . '">' . $referrer . '</a>';
    }
}
