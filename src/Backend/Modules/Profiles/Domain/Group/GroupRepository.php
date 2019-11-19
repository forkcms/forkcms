<?php

namespace Backend\Modules\Profiles\Domain\Group;

use Doctrine\ORM\EntityRepository;

final class GroupRepository extends EntityRepository
{
    public function add(Group $group): void
    {
        $this->getEntityManager()->persist($group);
        $this->getEntityManager()->flush();
    }

    public function remove(Group $group): void
    {
        $this->getEntityManager()->remove($group);
        $this->getEntityManager()->flush();
    }

    public function existsByName(string $name, int $excludedGroupId = 0) : bool
    {
        $query = $this->createQueryBuilder('g')
            ->where('g.name = :name')
            ->setParameter(':name', $name);

        if ($excludedGroupId !== 0) {
            $query
                ->andWhere('g.id != :id')
                ->setParameter(':id', $excludedGroupId);
        }

        return $query->getQuery()->getOneOrNullResult() instanceof Group;
    }

    public function findWithExcludedIds(array $excludeIds): array
    {
        $queryBuilder = $this->createQueryBuilder('g');

        if (empty($excludeIds)) {
            return $this->findAll();
        }

        return $queryBuilder
            ->where($queryBuilder->expr()->notIn('g.id', $excludeIds))
            ->getQuery()
            ->getResult();
    }
}
