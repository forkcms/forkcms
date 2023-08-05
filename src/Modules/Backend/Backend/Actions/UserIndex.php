<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use Doctrine\ORM\QueryBuilder;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
use ForkCMS\Modules\Backend\Domain\User\User;
use Symfony\Component\HttpFoundation\Request;

/**
 *Overview of the available users in the backend.
 */
final class UserIndex extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        $this->renderDataGrid(
            User::class,
            static function (QueryBuilder $queryBuilder): void {
                $queryBuilder->andWhere('User.deletedAt IS NULL');
            }
        );
    }
}
