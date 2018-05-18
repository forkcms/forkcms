<?php

namespace Backend\Modules\Profiles\Domain\ProfileGroupRight;

use Doctrine\ORM\EntityRepository;

final class ProfileGroupRightRepository extends EntityRepository
{
    public function add(ProfileGroupRight $groupRight): void
    {
        $this->getEntityManager()->persist($groupRight);
        $this->getEntityManager()->flush();
    }

    public function remove(ProfileGroupRight $groupRight): void
    {
        $this->getEntityManager()->remove($groupRight);
        $this->getEntityManager()->flush();
    }
}
