<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Frontend\Modules\Profiles\Engine\Model;
use Common\WebTestCase;
use Frontend\Modules\Profiles\Engine\Profile;

final class ModelTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Frontend');
        }

        $client = self::createClient();
        $this->loadFixtures($client);
    }

    public function testInsertingProfile(): void
    {
        $profileId = $this->addProfile();

        $profileData = $this->getProfileData();
        $addedProfile = Model::get($profileId);

        $this->assertInstanceOf(Profile::class, $addedProfile);

        $this->assertEquals($profileId, $addedProfile->getId());
        $this->assertEquals($profileData['email'], $addedProfile->getEmail());
        $this->assertEquals($profileData['status'], $addedProfile->getStatus());
        $this->assertEquals($profileData['display_name'], $addedProfile->getDisplayName());
        $this->assertEquals($profileData['url'], $addedProfile->getUrl());

        $this->assertEquals($profileId, Model::getIdByEmail($profileData['email']));
    }

    public function testProfileExists(): void
    {
        $profileData = $this->getProfileData();

        $this->assertFalse(Model::existsDisplayName($profileData['display_name']));
        $this->assertFalse(Model::existsByEmail($profileData['email']));

        $this->addProfile();

        $this->assertTrue(Model::existsDisplayName($profileData['display_name']));
        $this->assertTrue(Model::existsByEmail($profileData['email']));
    }

    public function testPasswordGetsEncrypted(): void
    {
        $encryptedPassword = Model::encryptPassword('Fork CMS');

        $this->assertTrue(password_verify('Fork CMS', $encryptedPassword));
    }

    public function testGettingEncryptedPassword(): void
    {
        $this->addProfile();

        $this->assertEquals(
            '$2y$10$1Ev9QQNYZBjdU1ELKjKNqelcV.j2l3CgtVkHl0aMvbNpg1g73S5lC',
            Model::getEncryptedPassword('test@fork-cms.com')
        );
    }

    public function testVerifyingPassword(): void
    {
        $this->addProfile();

        $this->assertTrue(Model::verifyPassword('test@fork-cms.com', 'forkcms'));
    }

    public function testSettingSettings(): void
    {
        Model::setSetting(1, 'my_setting', 'My setting\'s value');
        $this->assertEquals('My setting\'s value', Model::getSetting(1, 'my_setting'));

        Model::setSetting(1, 'my_setting', 'My updated value');
        $this->assertEquals('My updated value', Model::getSetting(1, 'my_setting'));

        Model::setSettings(
            1,
            [
                'my_setting' => 'Another updated value',
                'my_other_setting' => 'A new value',
            ]
        );

        $this->assertEquals('Another updated value', Model::getSetting(1, 'my_setting'));
        $this->assertEquals('A new value', Model::getSetting(1, 'my_other_setting'));
    }

    public function testGettingSettings(): void
    {
        Model::setSetting(1, 'my_setting', 'My setting\'s value');
        Model::setSetting(1, 'my_array', ['one', 'two', 'banana']);
        Model::setSetting(2, 'someone_elses_setting', 'Someone else\'s setting\'s value');

        $settings = Model::getSettings(1);

        $this->assertContains('My setting\'s value', $settings);
        $this->assertContains(['one', 'two', 'banana'], $settings);
        $this->assertNotContains('Someone else\'s setting\'s value', $settings);
    }

    public  function testDeletingSetting(): void
    {
        Model::setSetting(1, 'my_setting', 'My setting\'s value');
        $this->assertEquals('My setting\'s value', Model::getSetting(1, 'my_setting'));

        Model::deleteSetting(1, 'my_setting');
        $this->assertEquals('', Model::getSetting(1, 'my_setting'));
    }

    public function testGettingId(): void
    {
        $this->addProfile();

        $this->assertEquals(1, Model::getIdByEmail('test@fork-cms.com'));

        Model::setSetting(1, 'get_my_id', 'with_a_setting');
        $this->assertEquals(1, Model::getIdBySetting('get_my_id', 'with_a_setting'));
    }

    public function testGettingRandomString()
    {
        $this->assertNotEmpty(Model::getRandomString());
        $this->assertEquals(15, strlen(Model::getRandomString()));
        $this->assertEquals(14, strlen(Model::getRandomString(14)));
        $this->assertEquals(1, preg_match("#^[0-9]+$#", Model::getRandomString(15, true, false, false, false)));
        $this->assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, true, false, false)));
        $this->assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, true, true, false)));
        $this->assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, true, true, true)));
        $this->assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, false, true, false)));
        $this->assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, false, true, true)));
        $this->assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, false, false, true)));
        $this->assertEquals(1, preg_match("#^[a-z]+$#", Model::getRandomString(15, false, true, false, false)));
        $this->assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, true, false, false, false)));
        $this->assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, true, false, true, false)));
        $this->assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, true, false, true, true)));
        $this->assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, false, false, true, false)));
        $this->assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, false, false, true, true)));
        $this->assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, false, false, false, true)));
        $this->assertEquals(1, preg_match("#^[A-Z]+$#", Model::getRandomString(15, false, false, true, false)));
        $this->assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, true, false, false, false)));
        $this->assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, true, true, false, false)));
        $this->assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, true, true, false, true)));
        $this->assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, false, true, false, false)));
        $this->assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, false, true, false, true)));
        $this->assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, false, false, false, true)));
        $this->assertEquals(
            1,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, false, false, false, true)
            )
        );
        $this->assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, true, false, false, false)
            )
        );
        $this->assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, true, true, false, false)
            )
        );
        $this->assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, true, true, true, false)
            )
        );
        $this->assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, false, true, false, false)
            )
        );
        $this->assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, false, true, true, false)
            )
        );
        $this->assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, false, false, true, false)
            )
        );
    }

    public function testGettingUrl(): void
    {
        $firstUrl = Model::getUrl('Fork CMS');
        $this->assertEquals('fork-cms', $firstUrl);

        $this->addProfile();

        $secondUrl = Model::getUrl('Fork CMS');
        $this->assertEquals('fork-cms-2', $secondUrl);
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
