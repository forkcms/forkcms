<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Datagrid as BackendDataGrid;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;

/**
 * This page will display the statistical overview of bounces for a specified mailing
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class StatisticsBounces extends BackendBaseActionIndex
{
    // maximum number of items
    const PAGING_LIMIT = 20;

    /**
     * The list with bounces
     *
     * @var    array
     */
    private $bounces;

    /**
     * The given mailing record
     *
     * @var    array
     */
    private $mailing;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
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
        // get parameters
        $id = $this->getParameter('mailing_id', 'int');

        // does the item exist
        if (!BackendMailmotorModel::existsMailing($id)) {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&error=mailing-does-not-exist'
            );
        }

        // fetch the mailing
        $this->mailing = BackendMailmotorModel::getMailing($id);

        // fetch the bounces
        $this->bounces = BackendMailmotorCMHelper::getBounces($this->mailing['id']);

        // does the item exist
        if (empty($this->bounces)) {
            $this->redirect(
                BackendModel::createURLForAction('Statistics') . '&id=' . $this->mailing['id'] . '&error=no-bounces'
            );
        }
    }

    /**
     * Loads the datagrid with the clicked link
     */
    private function loadDataGrid()
    {
        // create a new source-object
        $source = new \SpoonDataGridSourceArray($this->bounces);

        // call the parent, as in create a new datagrid with the created source
        $this->dataGrid = new BackendDataGrid($source);
        $this->dataGrid->setURL(
            BackendModel::createURLForAction(
                'StatisticsBounces'
            ) . '&offset=[offset]&order=[order]&sort=[sort]&mailing_id=' . $this->mailing['id']
        );

        // hide the following columns
        $this->dataGrid->setColumnHidden('list_id');

        // sorting columns
        $this->dataGrid->setSortingColumns(array('email', 'bounce_type'), 'email');

        // set paging limit
        $this->dataGrid->setPagingLimit(self::PAGING_LIMIT);
    }

    /**
     * Parse all datagrids
     */
    protected function parse()
    {
        parent::parse();

        // parse the datagrid
        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());

        // parse mailing record
        $this->tpl->assign('mailing', $this->mailing);
    }
}
