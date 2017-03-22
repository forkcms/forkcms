<?php

namespace Backend\Modules\Search\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the statistics-action, it will display the overview of search statistics
 */
class Statistics extends BackendBaseActionIndex
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
            BackendSearchModel::QRY_DATAGRID_BROWSE_STATISTICS,
            BL::getWorkingLanguage()
        );

        // hide column
        $this->dataGrid->setColumnsHidden('data');

        // create column
        $this->dataGrid->addColumn('referrer', BL::lbl('Referrer'));

        // header labels
        $this->dataGrid->setHeaderLabels(array('time' => \SpoonFilter::ucfirst(BL::lbl('SearchedOn'))));

        // set column function
        $this->dataGrid->setColumnFunction(array(__CLASS__, 'setReferrer'), '[data]', 'referrer');
        $this->dataGrid->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getLongDate'),
            array('[time]'),
            'time',
            true
        );
        $this->dataGrid->setColumnFunction('htmlspecialchars', ['[term]'], 'term');

        // sorting columns
        $this->dataGrid->setSortingColumns(array('time', 'term'), 'time');
        $this->dataGrid->setSortParameter('desc');
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

    /**
     * Set column referrer
     *
     * @param string $data The source data.
     *
     * @return string
     */
    public static function setReferrer($data)
    {
        $data = unserialize($data);
        if (!isset($data['server']['HTTP_REFERER'])) {
            return '';
        }

        $referrer = htmlspecialchars($data['server']['HTTP_REFERER']);

        return '<a href="' . $referrer . '">' . $referrer . '</a>';
    }
}
