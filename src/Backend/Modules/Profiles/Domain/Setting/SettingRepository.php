<?php

namespace Backend\Modules\Profiles\Domain\Setting;

use Doctrine\ORM\EntityRepository;

final class SettingRepository extends EntityRepository
{
    public function add(Setting $Setting): void
    {
        $this->getEntityManager()->persist($Setting);
        $this->getEntityManager()->flush();
    }

    public function remove(Setting $setting): void
    {
        $this->getEntityManager()->remove($setting);
        $this->getEntityManager()->flush();
    }
}
