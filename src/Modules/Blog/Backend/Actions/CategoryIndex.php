<?php

namespace ForkCMS\Modules\Blog\Backend\Actions;

use Doctrine\ORM\QueryBuilder;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
use ForkCMS\Modules\Blog\Domain\Category\Category;
use Symfony\Component\HttpFoundation\Request;

class CategoryIndex extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        $locale = $this->translator->getLocale();

        $this->renderDataGrid(
            Category::class,
            static function (QueryBuilder $queryBuilder) use ($locale): void {
                $queryBuilder
                    ->andWhere('Category.locale = :locale')
                    ->setParameter('locale', $locale)
                ;
            }
        );
    }
}
