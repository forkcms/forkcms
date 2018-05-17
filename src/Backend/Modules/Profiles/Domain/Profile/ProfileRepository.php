<?php

namespace Backend\Modules\Profiles\Domain\Profile;

use Doctrine\ORM\EntityRepository;

class ProfileRepository extends EntityRepository
{
    public function add(Profile $profile): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($profile);
    }
}
