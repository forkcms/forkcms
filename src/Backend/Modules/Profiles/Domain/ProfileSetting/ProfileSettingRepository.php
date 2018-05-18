<?php

namespace Backend\Modules\Profiles\Domain\ProfileSetting;

use Doctrine\ORM\EntityRepository;

final class ProfileSettingRepository extends EntityRepository
{
    public function add(ProfileSetting $profileSetting): void
    {
        $this->getEntityManager()->persist($profileSetting);
        $this->getEntityManager()->flush();
    }

    public function remove(ProfileSetting $setting): void
    {
        $this->getEntityManager()->remove($setting);
        $this->getEntityManager()->flush();
    }
}
