<?php

namespace Backend\Modules\Pages\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

/**
 * This is the index-action (default), it will display the pages-overview
 */
class Index extends BackendBaseActionIndex
{
    /**
     * DataGrids
     *
     * @var BackendDataGridDatabase
     */
    private $dgDrafts;
    private $dgRecentlyEdited;

    public function execute(): void
    {
        parent::execute();

        // add js
        $this->header->addJS('jstree/jquery.tree.js', null, false);
        $this->header->addJS('jstree/lib/jquery.cookie.js', null, false);
        $this->header->addJS('jstree/plugins/jquery.tree.cookie.js', null, false);

        // load the dgRecentlyEdited
        $this->loadDataGrids();

        // parse
        $this->parse();

        // display the page
        $this->display();
    }

    private function loadDataGridDrafts(): void
    {
        // create datagrid
        $this->dgDrafts = new BackendDataGridDatabase(
            BackendPagesModel::QUERY_DATAGRID_BROWSE_DRAFTS,
            ['draft', BackendAuthentication::getUser()->getUserId(), BL::getWorkingLanguage()]
        );

        // hide columns
        $this->dgDrafts->setColumnsHidden(['revision_id']);

        // disable paging
        $this->dgDrafts->setPaging(false);

        // set column functions
        $this->dgDrafts->setColumnFunction(
            [new BackendDataGridFunctions(), 'getUser'],
            ['[user_id]'],
            'user_id',
            true
        );
        $this->dgDrafts->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[edited_on]'],
            'edited_on'
        );

        // set headers
        $this->dgDrafts->setHeaderLabels(
            [
                 'user_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
                 'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEdited')),
            ]
        );

        // check if allowed to edit
        if (BackendAuthentication::isAllowedAction('Edit', $this->getModule())) {
            // set column URLs
            $this->dgDrafts->setColumnURL(
                'title',
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]&amp;draft=[revision_id]'
            );

            // add edit column
            $this->dgDrafts->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]&amp;draft=[revision_id]',
                BL::lbl('Edit')
            );
        }
    }

    private function loadDataGridRecentlyEdited(): void
    {
        // create dgRecentlyEdited
        $this->dgRecentlyEdited = new BackendDataGridDatabase(
            BackendPagesModel::QUERY_BROWSE_RECENT,
            ['active', BL::getWorkingLanguage(), 7]
        );

        // disable paging
        $this->dgRecentlyEdited->setPaging(false);

        // hide columns
        $this->dgRecentlyEdited->setColumnsHidden(['id']);

        // set functions
        $this->dgRecentlyEdited->setColumnFunction(
            [new BackendDataGridFunctions(), 'getUser'],
            ['[user_id]'],
            'user_id'
        );
        $this->dgRecentlyEdited->setColumnFunction(
            [new BackendDataGridFunctions(), 'getTimeAgo'],
            ['[edited_on]'],
            'edited_on'
        );

        // set headers
        $this->dgRecentlyEdited->setHeaderLabels(
            [
                 'user_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
                 'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEdited')),
            ]
        );

        // check if allowed to edit
        if (BackendAuthentication::isAllowedAction('Edit', $this->getModule())) {
            // set column URL
            $this->dgRecentlyEdited->setColumnURL(
                'title',
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );

            // add column
            $this->dgRecentlyEdited->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }
    }

    private function loadDataGrids(): void
    {
        // load the datagrid with the recently edited items
        $this->loadDataGridRecentlyEdited();

        // load the dategird with the drafts
        $this->loadDataGridDrafts();
    }

    protected function parse(): void
    {
        parent::parse();

        // parse dgRecentlyEdited
        $this->template->assign(
            'dgRecentlyEdited',
            ($this->dgRecentlyEdited->getNumResults() != 0) ? $this->dgRecentlyEdited->getContent() : false
        );
        $this->template->assign('dgDrafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);

        // parse the tree
        $this->template->assign('tree', BackendPagesModel::getTreeHTML());

        // open the tree on a specific page
        if ($this->getRequest()->query->getInt('id') !== 0) {
            $this->template->assign(
                'openedPageId',
                $this->getRequest()->query->getInt('id')
            );
        } else {
            $this->template->assign('openedPageId', 1);
        }
    }
}
