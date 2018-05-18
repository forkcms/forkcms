<?php

namespace Backend\Modules\Profiles\Domain\ProfileGroup;

use Doctrine\ORM\EntityRepository;

final class ProfileGroupRepository extends EntityRepository
{
    public function add(ProfileGroup $group): void
    {
        $this->getEntityManager()->persist($group);
        $this->getEntityManager()->flush();
    }

    public function remove(ProfileGroup $group): void
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

        return $query->getQuery()->getOneOrNullResult() instanceof ProfileGroup;
    }
}
