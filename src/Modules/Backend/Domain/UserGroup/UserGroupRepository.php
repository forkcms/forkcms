<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Backend\Domain\UserGroup\Command\CreateUserGroup;
use InvalidArgumentException;
use Throwable;

/**
 * @method UserGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroup[] findAll()
 * @method UserGroup[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<UserGroup>
 */
final class UserGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        try {
            parent::__construct($registry, UserGroup::class);
        } catch (Throwable $throwable) {
            if (!empty($_ENV['FORK_DATABASE_HOST']) && $_ENV['APP_ENV'] !== 'test') {
                throw $throwable; // needed during the installer
            }
        }
    }

    public function save(UserGroup $userGroup): void
    {
        $this->getEntityManager()->persist($userGroup);
        $this->getEntityManager()->flush();
    }

    public function remove(UserGroup $userGroup): void
    {
        if ($userGroup->getId() === UserGroup::ADMIN_GROUP_ID) {
            throw new InvalidArgumentException('Deleting the admin group is not allowed');
        }

        $entityManager = $this->getEntityManager();
        $entityManager->remove($userGroup);
        $entityManager->flush();
    }

    public function getAdminUserGroup(): UserGroup
    {
        $adminUserGroup = $this->findOneBy(['id' => UserGroup::ADMIN_GROUP_ID]);
        if (!$adminUserGroup instanceof UserGroup) {
            $userGroupDataTransferObject = new CreateUserGroup();
            $userGroupDataTransferObject->name = 'Admin';
            $adminUserGroup = UserGroup::fromDataTransferObject($userGroupDataTransferObject);
            $this->save($adminUserGroup);
        }

        return $adminUserGroup;
    }
}
