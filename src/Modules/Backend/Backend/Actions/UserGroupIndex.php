<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use Symfony\Component\HttpFoundation\Request;

/**
 *Overview of the available groups in the backend.
 */
final class UserGroupIndex extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        $this->renderDataGrid(
            UserGroup::class,
            static function (QueryBuilder $queryBuilder): void {
                $queryBuilder
                    ->leftJoin('UserGroup.users', 'Users', Join::WITH, 'Users.deletedAt IS NULL')
                    ->addSelect('Users');
            }
        );
    }
}
