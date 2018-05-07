<?php

namespace Backend\Modules\Location\Domain\Location;

use Doctrine\ORM\EntityRepository;

final class LocationRepository extends EntityRepository
{
    public function add(Location $location): void
    {
        $this->getEntityManager()->persist($location);
        $this->getEntityManager()->flush();
    }

    public function save(Location $location): void
    {
        $this->getEntityManager()->flush($location);
    }

    public function remove(Location $location): void
    {
        $this->getEntityManager()->remove($location);
        $this->getEntityManager()->flush();
    }
}
