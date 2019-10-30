<?php

namespace Backend\Modules\Profiles\Domain\GroupRight;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Doctrine\ORM\EntityRepository;

final class GroupRightRepository extends EntityRepository
{
    public function add(GroupRight $groupRight): void
    {
        $this->getEntityManager()->persist($groupRight);
        $this->getEntityManager()->flush();
    }

    public function remove(GroupRight $groupRight): void
    {
        $this->getEntityManager()->remove($groupRight);
        $this->getEntityManager()->flush();
    }

    public function findLinkedToProfile(?Profile $profile, int $includeId = null) : array
    {
        if ($profile === null) {
            return $this->findAll();
        }

        $query = $this->createQueryBuilder('r')
            ->where('r.profile = :profile')
            ->setParameter(':profile', $profile);

        if ($includeId !== null) {
            $query
                ->andWhere('r.id != :id')
                ->setParameter(':id', $includeId);
        }

        return $query->getQuery()->getResult();
    }
}
