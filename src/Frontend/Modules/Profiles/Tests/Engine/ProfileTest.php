<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\Profile\Status;
use Frontend\Modules\Profiles\Engine\Model;
use Common\WebTestCase;

final class ProfileTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Frontend');
        }

        if (!defined('LANGUAGE')) {
            define('LANGUAGE', 'en');
        }

        $client = self::createClient();
        $this->loadFixtures($client);
    }

    public function testCreatingProfile(): void
    {
        $profile = new Profile(
            'info@fork-cms.com',
            'forkcms',
            Status::active(),
            'Fork CMS',
            'fork-cms'
        );

        $this->assertEquals('Fork CMS', $profile->getDisplayName());
        $this->assertEquals('info@fork-cms.com', $profile->getEmail());
        $this->assertEquals('active', $profile->getStatus());
        $this->assertEquals('fork-cms', $profile->getUrl());

        $profile->setSettings(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ]
        );

        $settings = [];
        foreach ($profile->getSettings()->toArray() as $profileSetting) {
            $settings[$profileSetting->getName()] = $profileSetting->getValue();
        }
        $this->assertEquals(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ],
            $settings
        );
        $this->assertEquals('My first value', $profile->getSetting('my_first_setting'));

        $profile->setSetting('my_second_setting', 'My updated value');
        $this->assertEquals('My updated value', $profile->getSetting('my_second_setting'));

        $profileArray = $profile->toArray();
        $this->assertArrayHasKey('display_name', $profileArray);
        $this->assertEquals('Fork CMS', $profileArray['display_name']);
    }

    public function testLoadingOfProfile(): void
    {
        $profileId = $this->addProfile();

        $profile = Model::get($profileId);

        $this->assertEquals('Fork CMS', $profile->getDisplayName());
        $this->assertEquals('test@fork-cms.com', $profile->getEmail());
        $this->assertEquals('active', $profile->getStatus());
        $this->assertEquals('fork-cms', $profile->getUrl());

        $profile->setSettings(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ]
        );

        $settings = [];
        foreach ($profile->getSettings()->toArray() as $profileSetting) {
            $settings[$profileSetting->getName()] = $profileSetting->getValue();
        }
        $this->assertEquals(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ],
            $settings
        );
        $this->assertEquals('My first value', $profile->getSetting('my_first_setting'));

        $profile->setSetting('my_second_setting', 'My updated value');
        $this->assertEquals('My updated value', $profile->getSetting('my_second_setting'));

        $profileArray = $profile->toArray();
        $this->assertArrayHasKey('display_name', $profileArray);
        $this->assertEquals('Fork CMS', $profileArray['display_name']);
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
