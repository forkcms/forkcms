<?php

namespace ForkCMS\Modules\Pages\Backend\Actions;

use Doctrine\ORM\QueryBuilder;
use ForkCMS\Core\Backend\Domain\DataGrid\DataGridFunctions as BackendDataGridFunctions;
use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Core\Backend\Helper\Model as BackendModel;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\NavigationBuilder;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Backend\Helper\Model as BackendPagesModel;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is the index-action (default), it will display the pages-overview
 */
class PageIndex extends AbstractActionController
{
    public function __construct(ActionServices $actionServices, private readonly NavigationBuilder $navigationBuilder)
    {
        parent::__construct($actionServices);
    }

    protected function execute(Request $request): void
    {
        $this->assign('sidebarTree', $this->navigationBuilder->getTree(Locale::request()));
        $this->assign(
            'lastEditedDataGrid',
            $this->dataGridFactory->forEntity(
                Revision::class,
                static function (QueryBuilder $queryBuilder) use ($request): void {
                    $queryBuilder
                        ->andWhere('Revision.locale = :locale')
                        ->innerJoin('Revision.page', 'Page')
                        ->addSelect('Page')
                        ->setParameter('locale', $request->getLocale())
                        ->orderBy('Revision.updatedOn', 'DESC')
                        ->setMaxResults(10);
                },
                10
            )
        );
        return;
        // load the dgRecentlyEdited
        $this->loadDataGrids();

        // parse
        $this->parse();
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
        $this->dgDrafts->setColumnFunction('htmlspecialchars', ['[title]'], 'title', false);

        // set headers
        $this->dgDrafts->setHeaderLabels(
            [
                'user_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
                'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEdited')),
            ]
        );

        // check if allowed to edit
        if (BackendAuthentication::isAllowedAction('PageEdit', $this->getModule())) {
            // set column URLs
            $this->dgDrafts->setColumnURL(
                'title',
                BackendModel::createUrlForAction('PageEdit') . '&amp;id=[id]&amp;draft=[revision_id]'
            );

            // add edit column
            $this->dgDrafts->addColumnAction(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('PageEdit') . '&amp;id=[id]&amp;draft=[revision_id]',
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
        $this->dgRecentlyEdited->setColumnFunction('htmlspecialchars', ['[title]'], 'title', false);

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

        if (BackendAuthentication::isAllowedAction('PageAdd', $this->getModule())) {
            $this->dgRecentlyEdited->addColumnAction(
                'copy',
                null,
                BL::lbl('Copy'),
                BackendModel::createUrlForAction('PageAdd') . '&amp;copy=[id]',
                BL::lbl('Copy')
            );
        }

        // check if allowed to edit
        if (BackendAuthentication::isAllowedAction('PageEdit', $this->getModule())) {
            // set column URL
            $this->dgRecentlyEdited->setColumnURL(
                'title',
                BackendModel::createUrlForAction('PageEdit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );

            // add column
            $this->dgRecentlyEdited->addColumnAction(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('PageEdit') . '&amp;id=[id]',
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
        $this->template->assign(
            'dgDrafts',
            ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false
        );

        // parse the tree
        $this->template->assign('tree', BackendPagesModel::getTreeHTML());

        // open the tree on a specific page
        if ($this->getRequest()->query->getInt('id') !== 0) {
            $this->template->assign(
                'openedPageId',
                $this->getRequest()->query->getInt('id')
            );
        } else {
            $this->template->assign('openedPageId', Page::HOME_PAGE_ID);
        }
    }
}
