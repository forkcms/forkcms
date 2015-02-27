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
use Backend\Core\Engine\Datagrid as BackendDataGrid;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;

/**
 * This page will display the statistical overview of a sent mailing
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Statistics extends BackendBaseActionIndex
{
    // maximum number of items
    const PAGING_LIMIT = 10;

    /**
     * The given mailing ID
     *
     * @var    int
     */
    private $id;

    /**
     * The mailing record
     *
     * @var    array
     */
    private $mailing;

    /**
     * The statistics record
     *
     * @var    array
     */
    private $statistics;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->header->addJS('highcharts.js', 'Core', false);
        $this->getData();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    /**
     * Gets all data needed for this page
     */
    private function getData()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if (!BackendMailmotorModel::existsMailing($this->id)) {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&amp;error=mailing-does-not-exist'
            );
        }

        // get mailing
        $this->mailing = BackendMailmotorModel::getMailing($this->id);

        // fetch the statistics
        $this->statistics = BackendMailmotorCMHelper::getStatistics($this->id, true);

        // no stats found
        if ($this->statistics === false) {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&amp;error=no-statistics-loaded&amp;var=' . str_replace(
                    '#',
                    '',
                    $this->mailing['name']
                )
            );
        }
    }

    /**
     * Loads the datagrid with the clicked link
     */
    private function loadDataGrid()
    {
        // no statistics found
        if (empty($this->statistics['clicked_links'])) {
            return false;
        }

        // map urlencode to clicked links stack
        $this->statistics['clicked_links'] = \SpoonFilter::arrayMapRecursive(
            'urlencode',
            $this->statistics['clicked_links']
        );

        // create a new source-object
        $source = new \SpoonDataGridSourceArray($this->statistics['clicked_links']);

        // call the parent, as in create a new datagrid with the created source
        $this->dataGrid = new BackendDataGrid($source);
        $this->dataGrid->setURL(
            BackendModel::createURLForAction() . '&offset=[offset]&order=[order]&sort=[sort]&id=' . $this->id
        );

        // set headers values
        $headers['link'] = strtoupper(BL::lbl('URL'));
        $headers['clicks'] = \SpoonFilter::ucfirst(BL::msg('ClicksAmount'));

        // set headers
        $this->dataGrid->setHeaderLabels($headers);

        // sorting columns
        $this->dataGrid->setSortingColumns(array('link', 'clicks'), 'link');

        // set column functions
        $this->dataGrid->setColumnFunction('urldecode', array('[link]'), 'link', true);
        $this->dataGrid->setColumnFunction('urldecode', array('[link]'), 'link', true);

        // set paging limit
        $this->dataGrid->setPagingLimit(static::PAGING_LIMIT);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('StatisticsLink')) {
            // add edit column
            $this->dataGrid->addColumnAction(
                'users',
                null,
                BL::lbl('Who'),
                BackendModel::createURLForAction('StatisticsLink') . '&amp;url=[link]&amp;mailing_id=' . $this->id,
                BL::lbl('Who')
            );
        }
    }

    /**
     * Parse all datagrids
     */
    protected function parse()
    {
        parent::parse();

        // parse the datagrid
        if (!empty($this->statistics['clicked_links'])) {
            $this->tpl->assign(
                'dataGrid',
                (string) $this->dataGrid->getContent()
            );
        }

        // parse the mailing record
        $this->tpl->assign('mailing', $this->mailing);

        // parse statistics
        $this->tpl->assign('stats', $this->statistics);
    }
}
