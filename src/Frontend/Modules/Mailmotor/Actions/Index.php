<?php

namespace Frontend\Modules\Mailmotor\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Mailmotor\Engine\Model as FrontendMailmotorModel;

/**
 * This is the index-action
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Index extends FrontendBaseBlock
{
    const MAILINGS_PAGING_LIMIT = 10;

    /**
     * The data grid object
     *
     * @var    \SpoonDataGrid
     */
    private $dataGrid;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->tpl->assign('hideContentTitle', true);
        $this->loadTemplate();
        $this->loadDataGrid();
        $this->parseDataGrid();
    }

    /**
     * Load the data grid
     */
    private function loadDataGrid()
    {
        // create a new source-object
        $source = new \SpoonDataGridSourceDB(
            FrontendModel::getContainer()->get('database'),
            array(FrontendMailmotorModel::QRY_DATAGRID_BROWSE_SENT, array('sent', FRONTEND_LANGUAGE))
        );

        // create data grid
        $this->dataGrid = new \SpoonDataGrid($source);
        $this->dataGrid->setCompileDirectory(FRONTEND_CACHE_PATH . '/CompiledTemplates');

        // set hidden columns
        $this->dataGrid->setColumnsHidden(array('id', 'status'));

        // set headers values
        $headers['name'] = \SpoonFilter::ucfirst(FL::lbl('Name'));
        $headers['send_on'] = \SpoonFilter::ucfirst(FL::lbl('Sent'));

        // set headers
        $this->dataGrid->setHeaderLabels($headers);

        // sorting columns
        $this->dataGrid->setSortingColumns(array('name', 'send_on'), 'name');
        $this->dataGrid->setSortParameter('desc');

        // set column URLs
        $this->dataGrid->setColumnURL('name', FrontendNavigation::getURLForBlock('Mailmotor', 'Detail') . '/[id]');

        // set column functions
        $this->dataGrid->setColumnFunction(array('SpoonDate', 'getTimeAgo'), array('[send_on]'), 'send_on', true);

        // add styles
        $this->dataGrid->setColumnAttributes('name', array('class' => 'title'));

        // set paging limit
        $this->dataGrid->setPagingLimit(self::MAILINGS_PAGING_LIMIT);
    }

    /**
     * parse the data grid
     */
    private function parseDataGrid()
    {
        // parse the data grid in the template
        $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
    }
}
