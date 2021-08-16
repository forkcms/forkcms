<?php

namespace Backend\Modules\Location\Domain\LocationSetting;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LocationSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationSetting[] findAll()
 * @method LocationSetting[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class LocationSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationSetting::class);
    }

    public function add(LocationSetting $setting): void
    {
        $this->getEntityManager()->persist($setting);
        $this->getEntityManager()->flush();
    }

    public function save(LocationSetting $setting): void
    {
        $this->getEntityManager()->flush($setting);
    }

    public function remove(LocationSetting $setting): void
    {
        $this->getEntityManager()->remove($setting);
        $this->getEntityManager()->flush();
    }
}
