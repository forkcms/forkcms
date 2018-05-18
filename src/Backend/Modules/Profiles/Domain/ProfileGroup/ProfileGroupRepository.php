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
}
