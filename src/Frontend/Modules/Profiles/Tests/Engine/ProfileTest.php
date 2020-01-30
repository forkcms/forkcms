<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Frontend\Modules\Profiles\Engine\Model;
use Frontend\Core\Tests\FrontendWebTestCase;
use Frontend\Modules\Profiles\Engine\Profile;

final class ProfileTest extends FrontendWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreatingProfile(): void
    {
        $profile = new Profile();

        $profile->setDisplayName('Fork CMS');
        self::assertEquals('Fork CMS', $profile->getDisplayName());

        $profile->setEmail('info@fork-cms.com');
        self::assertEquals('info@fork-cms.com', $profile->getEmail());

        $profile->setRegisteredOn(1234567890);
        self::assertEquals(1234567890, $profile->getRegisteredOn());

        $profile->setStatus('random_status');
        self::assertEquals('random_status', $profile->getStatus());

        $profile->setUrl('fork-cms');
        self::assertEquals('fork-cms', $profile->getUrl());

        // @TODO These settings setters don't work because the profile doesn't have an ID, this should be fixed
        /*$profile->setSettings(
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
            $profile->getSettings()
        );
        self::assertEquals('My first value', $profile->getSetting('my_first_setting'));

        $profile->setSetting('my_second_setting', 'My updated value');
        self::assertEquals('My updated value', $profile->getSetting('my_second_setting'));

        $profileArray = $profile->toArray();
        self::assertArrayHasKey('display_name', $profileArray);
        self::assertEquals('Fork CMS', $profileArray['display_name']);
        self::assertArrayHasKey('registered_on', $profileArray);
        self::assertEquals(1234567890, $profileArray['registered_on']);
        */
    }

    public function testLoadingOfProfile(): void
    {
        $profileId = $this->addProfile();

        $profile = new Profile($profileId);

        self::assertEquals('Fork CMS', $profile->getDisplayName());
        self::assertEquals('test@fork-cms.com', $profile->getEmail());
        self::assertEquals(1520243112, $profile->getRegisteredOn());
        self::assertEquals('active', $profile->getStatus());
        self::assertEquals('fork-cms', $profile->getUrl());

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
            $profile->getSettings()
        );
        self::assertEquals('My first value', $profile->getSetting('my_first_setting'));

        $profile->setSetting('my_second_setting', 'My updated value');
        self::assertEquals('My updated value', $profile->getSetting('my_second_setting'));

        $profileArray = $profile->toArray();
        self::assertArrayHasKey('display_name', $profileArray);
        self::assertEquals('Fork CMS', $profileArray['display_name']);
        self::assertArrayHasKey('registered_on', $profileArray);
        self::assertEquals(1520243112, $profileArray['registered_on']);
    }

    public function addProfile(): int
    {
        return Model::insert($this->getProfileData());
    }

    public function getProfileData(): array
    {
        return [
            'email' => 'test@fork-cms.com',
            'password' => '$2y$10$1Ev9QQNYZBjdU1ELKjKNqelcV.j2l3CgtVkHl0aMvbNpg1g73S5lC',
            'status' => 'active',
            'display_name' => 'Fork CMS',
            'url' => 'fork-cms',
            'registered_on' => '2018-03-05 09:45:12',
            'last_login' => '1970-01-01 00:00:00',
        ];
    }
}
