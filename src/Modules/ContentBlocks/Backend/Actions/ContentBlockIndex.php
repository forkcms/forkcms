<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use Doctrine\ORM\QueryBuilder;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Status;
use Symfony\Component\HttpFoundation\Request;

/**
 * ContentBlock overview.
 */
final class ContentBlockIndex extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        $locale = $this->translator->getLocale();

        $this->renderDataGrid(
            ContentBlock::class,
            static function (QueryBuilder $queryBuilder) use ($locale): void {
                $queryBuilder
                    ->andWhere('ContentBlock.locale = :locale')
                    ->andWhere('ContentBlock.status = :active')
                    ->setParameter('locale', $locale)
                    ->setParameter('active', Status::Active)
                ;
            }
        );
    }
}
