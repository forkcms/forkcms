<?php

namespace Backend\Modules\Profiles\Domain\ProfileSession;

use Doctrine\ORM\EntityRepository;

final class ProfileSessionRepository extends EntityRepository
{
    public function add(ProfileSession $session): void
    {
        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();
    }

    public function remove(ProfileSession $session): void
    {
        $this->getEntityManager()->remove($session);
        $this->getEntityManager()->flush();
    }
}
