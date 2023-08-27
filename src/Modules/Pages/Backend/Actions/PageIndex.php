<?php

namespace ForkCMS\Modules\Pages\Backend\Actions;

use Doctrine\ORM\QueryBuilder;
use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\NavigationBuilder;
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
        $this->assign('sidebarTree', $this->navigationBuilder->getTree(Locale::current()));
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
        $this->assign(
            'draftDataGrid',
            $this->dataGridFactory->forEntity(
                Revision::class,
                static function (QueryBuilder $queryBuilder) use ($request): void {
                    $queryBuilder
                        ->andWhere('Revision.locale = :locale')
                        ->andWhere('Revision.isDraft = :isDraft')
                        ->innerJoin('Revision.page', 'Page')
                        ->addSelect('Page')
                        ->setParameter('locale', $request->getLocale())
                        ->setParameter('isDraft', true)
                        ->orderBy('Revision.updatedOn', 'DESC')
                        ->setMaxResults(10);
                },
                10
            )
        );
    }
}
