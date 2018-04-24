<?php

namespace Backend\Modules\Location\Domain\LocationSetting;

use Doctrine\ORM\EntityRepository;

final class LocationSettingRepository extends EntityRepository
{
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
