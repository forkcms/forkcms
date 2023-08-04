<?php

namespace Backend\Modules\Location\Domain\Location;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[] findAll()
 * @method Location[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

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
