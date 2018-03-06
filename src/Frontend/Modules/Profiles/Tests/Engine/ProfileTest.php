<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Frontend\Modules\Profiles\Engine\Model;
use Common\WebTestCase;
use Frontend\Modules\Profiles\Engine\Profile;

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
        $profile = new Profile();

        $profile->setDisplayName('Fork CMS');
        $this->assertEquals('Fork CMS', $profile->getDisplayName());

        $profile->setEmail('info@fork-cms.com');
        $this->assertEquals('info@fork-cms.com', $profile->getEmail());

        $profile->setRegisteredOn(1234567890);
        $this->assertEquals(1234567890, $profile->getRegisteredOn());

        $profile->setStatus('random_status');
        $this->assertEquals('random_status', $profile->getStatus());

        $profile->setUrl('fork-cms');
        $this->assertEquals('fork-cms', $profile->getUrl());

        // @TODO These settings setters don't work because the profile doesn't have an ID, this should be fixed
        /*$profile->setSettings(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ]
        );
        $this->assertEquals(
            [
                'my_first_setting' => 'My first value',
                'my_second_setting' => 'My second value',
            ],
            $profile->getSettings()
        );
        $this->assertEquals('My first value', $profile->getSetting('my_first_setting'));

        $profile->setSetting('my_second_setting', 'My updated value');
        $this->assertEquals('My updated value', $profile->getSetting('my_second_setting'));

        $profileArray = $profile->toArray();
        $this->assertArrayHasKey('display_name', $profileArray);
        $this->assertEquals('Fork CMS', $profileArray['display_name']);
        $this->assertArrayHasKey('registered_on', $profileArray);
        $this->assertEquals(1234567890, $profileArray['registered_on']);*/
    }
}
