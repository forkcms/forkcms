<?php

namespace Frontend\Modules\Profiles\Tests\DataFixtures;

use DateTime;
use SpoonDatabase;

class LoadProfiles
{
    public function load(SpoonDatabase $database): void
    {
        $dateWithinAMonthAgo = new DateTime('-1 week');
        $dateOverAMonthAgo = new DateTime('-2 months');

        $database->insert(
            'profiles_sessions',
            [
                [
                    'session_id' => '0123456789',
                    'profile_id' => 1,
                    'secret_key' => 'NotSoSecretNowIsIt',
                    'date' => $dateWithinAMonthAgo->format('Y-m-d H:i:s'),
                ],
                [
                    'session_id' => '1234567890',
                    'profile_id' => 1,
                    'secret_key' => 'WeNeedToTalk',
                    'date' => $dateOverAMonthAgo->format('Y-m-d H:i:s'),
                ],
            ]
        );

        $database->insert(
            'profiles',
            [
                [
                    'email' => 'test-active@fork-cms.com',
                    'password' => '$2y$10$vvFSouMBY97xF2e2axFryu8vd738pkjKtNnpJCbduWI5qqI/f4lYK', // forkcms
                    'status' => 'active',
                    'display_name' => 'active Fork CMS profile',
                    'url' => 'active-fork-cms-profile',
                    'registered_on' => $dateOverAMonthAgo->format('Y-m-d H:i:s'),
                    'last_login' => $dateWithinAMonthAgo->format('Y-m-d H:i:s'),
                ],
                [
                    'email' => 'test-inactive@fork-cms.com',
                    'password' => '$2y$10$vvFSouMBY97xF2e2axFryu8vd738pkjKtNnpJCbduWI5qqI/f4lYK', // forkcms
                    'status' => 'inactive',
                    'display_name' => 'inactive Fork CMS profile',
                    'url' => 'inactive-fork-cms-profile',
                    'registered_on' => $dateOverAMonthAgo->format('Y-m-d H:i:s'),
                    'last_login' => $dateWithinAMonthAgo->format('Y-m-d H:i:s'),
                ],
                [
                    'email' => 'test-deleted@fork-cms.com',
                    'password' => '$2y$10$vvFSouMBY97xF2e2axFryu8vd738pkjKtNnpJCbduWI5qqI/f4lYK', // forkcms
                    'status' => 'deleted',
                    'display_name' => 'deleted Fork CMS profile',
                    'url' => 'deleted-fork-cms-profile',
                    'registered_on' => $dateOverAMonthAgo->format('Y-m-d H:i:s'),
                    'last_login' => $dateWithinAMonthAgo->format('Y-m-d H:i:s'),
                ],
                [
                    'email' => 'test-blocked@fork-cms.com',
                    'password' => '$2y$10$vvFSouMBY97xF2e2axFryu8vd738pkjKtNnpJCbduWI5qqI/f4lYK', // forkcms
                    'status' => 'blocked',
                    'display_name' => 'blocked Fork CMS profile',
                    'url' => 'blocked-fork-cms-profile',
                    'registered_on' => $dateOverAMonthAgo->format('Y-m-d H:i:s'),
                    'last_login' => $dateWithinAMonthAgo->format('Y-m-d H:i:s'),
                ],
            ]
        );
    }
}
