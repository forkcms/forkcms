<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDoctrineQuery;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\LanguageName;
use Backend\Modules\ContentBlocks\ContentBlock\ContentBlockRepository;
use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;

/**
 * This is the index-action (default), it will display the overview
 */
class Index extends BackendBaseActionIndex
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
     * Load the datagrids
     */
    private function loadDataGrid()
    {
        /** @var ContentBlockRepository $contentBlockRepository */
        $contentBlockRepository = $this->get('doctrine.orm.entity_manager')->getRepository(ContentBlock::class);
        $this->dataGrid = new DataGridDoctrineQuery(
            $contentBlockRepository->getDataGridQuery(LanguageName::workingLanguage())
        );
        $this->dataGrid->setSortingColumns(['title']);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL(
                'title',
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]'
            );
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }
    }

    /**
     * Parse the datagrid and the reports
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
    }
}
