<?php

namespace Frontend\Modules\Profiles\Tests\DataFixtures;

use Backend\Modules\Profiles\DataFixtures\LoadProfilesProfile;
use SpoonDatabase;

/**
 * @deprecated remove this in Fork 6, just use the one from the Backend
 */
class LoadProfiles
{
    public function load(SpoonDatabase $database): void
    {
        (new LoadProfilesProfile())->load($database);
    }
}
