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
use Backend\Core\Engine\TemplateModifiers;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Locale;
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
            $contentBlockRepository->getDataGridQuery(Locale::workingLocale())
        );
        $this->dataGrid->setSortingColumns(['title']);

        // show the hidden status
        $this->dataGrid->addColumn('isHidden', ucfirst(BL::lbl('VisibleOnSite')), '[hidden]');
        $this->dataGrid->setColumnFunction([TemplateModifiers::class, 'showBool'], ['[hidden]', true], 'isHidden');

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
