<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\DataFixtures\LoadProfilesProfile;
use Frontend\Core\Tests\FrontendWebTestCase;
use Frontend\Modules\Profiles\Engine\Profile;
use Symfony\Bundle\FrameworkBundle\Client;
use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\Profile\Status;
use Frontend\Modules\Profiles\Engine\Model;
use Common\WebTestCase;

final class ProfileTest extends FrontendWebTestCase
{
    public function testCreatingProfile(): void
    {
        $profile = new Profile(
            'info@fork-cms.com',
            'forkcms',
            Status::active(),
            'Fork CMS',
            'fork-cms'
        );

        $profile->setDisplayName(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME, $profile->getDisplayName());

        $profile->setEmail(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL, $profile->getEmail());

        $profile->setRegisteredOn(LoadProfilesProfile::getDateOverAMonthAgo()->getTimestamp());
        self::assertEquals(LoadProfilesProfile::getDateOverAMonthAgo()->getTimestamp(), $profile->getRegisteredOn());

        $profile->setStatus(Status::inactive());
        self::assertEquals(Status::inactive(), $profile->getStatus());

        $profile->setUrl(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_URL);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_URL, $profile->getUrl());

        $profile->setSettings(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ]
        );
        self::assertEquals(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ],
            $settings
        );
        self::assertEquals('My first value', $profile->getSetting('my_first_setting'));

        $profile->setSetting('my_second_setting', 'My updated value');
        self::assertEquals('My updated value', $profile->getSetting('my_second_setting'));

        $profileArray = $profile->toArray();
        self::assertArrayHasKey('display_name', $profileArray);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME, $profileArray['display_name']);
        self::assertArrayHasKey('registered_on', $profileArray);
        self::assertEquals(LoadProfilesProfile::getDateOverAMonthAgo()->getTimestamp(), $profileArray['registered_on']);
    }

    public function testLoadingOfProfile(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        $profile = new Profile(LoadProfilesProfile::getProfileActiveId());

        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME, $profile->getDisplayName());
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL, $profile->getEmail());
        self::assertEquals(
            LoadProfilesProfile::getDateWithinAMonthAgo()->getTimestamp(),
            $profile->getRegisteredOn()
        );
        self::assertEquals('active', $profile->getStatus());
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_URL, $profile->getUrl());

        $profile->setSettings(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ]
        );
        self::assertEquals(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ],
            $settings
        );
        self::assertEquals('My first value', $profile->getSetting('my_first_setting'));

        $profile->setSetting('my_second_setting', 'My updated value');
        self::assertEquals('My updated value', $profile->getSetting('my_second_setting'));

        $profileArray = $profile->toArray();
        self::assertArrayHasKey('display_name', $profileArray);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME, $profileArray['display_name']);
        self::assertArrayHasKey('registered_on', $profileArray);
        self::assertEquals(
            LoadProfilesProfile::getDateWithinAMonthAgo()->getTimestamp(),
            $profileArray['registered_on']
        );
    }
}
