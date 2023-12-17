<?php

namespace ForkCMS\Modules\Blog\Backend\Actions;

use Doctrine\ORM\QueryBuilder;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Article\Status;
use Symfony\Component\HttpFoundation\Request;

class BlogIndex extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        $locale = $this->translator->getLocale();

        $this->renderDataGrid(
            Article::class,
            static function (QueryBuilder $queryBuilder) use ($locale): void {
                $queryBuilder
                    ->andWhere('Article.locale = :locale')
                    ->andWhere('Article.status != :archived')
                    ->setParameter('locale', $locale)
                    ->setParameter('archived', Status::ARCHIVED)
                ;
            }
        );
    }
}
