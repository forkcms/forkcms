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
            'ProfilesSession',
            [
                [
                    'sessionId' => '0123456789',
                    'profile_id' => 1,
                    'secretKey' => 'NotSoSecretNowIsIt',
                    'date' => $dateWithinAMonthAgo,
                ],
                [
                    'session_id' => '1234567890',
                    'profile_id' => 1,
                    'secret_key' => 'WeNeedToTalk',
                    'date' => $dateOverAMonthAgo,
                ],
            ]
        );

        $database->insert(
            'ProfilesProfile',
            [
                [
                    'email' => 'test-active@fork-cms.com',
                    'password' => '$2y$10$vvFSouMBY97xF2e2axFryu8vd738pkjKtNnpJCbduWI5qqI/f4lYK', // forkcms
                    'status' => 'active',
                    'displayName' => 'active Fork CMS profile',
                    'url' => 'active-fork-cms-profile',
                    'registeredOn' => $dateOverAMonthAgo,
                    'lastLogin' => $dateWithinAMonthAgo,
                ],
                [
                    'email' => 'test-inactive@fork-cms.com',
                    'password' => '$2y$10$vvFSouMBY97xF2e2axFryu8vd738pkjKtNnpJCbduWI5qqI/f4lYK', // forkcms
                    'status' => 'inactive',
                    'displayName' => 'inactive Fork CMS profile',
                    'url' => 'inactive-fork-cms-profile',
                    'registeredOn' => $dateOverAMonthAgo,
                    'lastLogin' => $dateWithinAMonthAgo,
                ],
                [
                    'email' => 'test-deleted@fork-cms.com',
                    'password' => '$2y$10$vvFSouMBY97xF2e2axFryu8vd738pkjKtNnpJCbduWI5qqI/f4lYK', // forkcms
                    'status' => 'deleted',
                    'displayName' => 'deleted Fork CMS profile',
                    'url' => 'deleted-fork-cms-profile',
                    'registeredOn' => $dateOverAMonthAgo,
                    'lastLogin' => $dateWithinAMonthAgo,
                ],
                [
                    'email' => 'test-blocked@fork-cms.com',
                    'password' => '$2y$10$vvFSouMBY97xF2e2axFryu8vd738pkjKtNnpJCbduWI5qqI/f4lYK', // forkcms
                    'status' => 'blocked',
                    'displayName' => 'blocked Fork CMS profile',
                    'url' => 'blocked-fork-cms-profile',
                    'registeredOn' => $dateOverAMonthAgo,
                    'lastLogin' => $dateWithinAMonthAgo,
                ],
            ]
        );
    }
}
