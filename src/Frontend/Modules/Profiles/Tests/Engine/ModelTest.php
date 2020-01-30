<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Frontend\Modules\Profiles\Engine\Model;
use Frontend\Core\Tests\FrontendWebTestCase;
use Frontend\Modules\Profiles\Engine\Profile;

final class ModelTest extends FrontendWebTestCase
{
    public function testInsertingProfile(): void
    {
        $profileId = $this->addProfile();

        $profileData = $this->getProfileData();
        $addedProfile = Model::get($profileId);

        self::assertInstanceOf(Profile::class, $addedProfile);

        self::assertEquals($profileId, $addedProfile->getId());
        self::assertEquals($profileData['email'], $addedProfile->getEmail());
        self::assertEquals($profileData['status'], $addedProfile->getStatus());
        self::assertEquals($profileData['display_name'], $addedProfile->getDisplayName());
        self::assertEquals($profileData['url'], $addedProfile->getUrl());

        self::assertEquals($profileId, Model::getIdByEmail($profileData['email']));
    }

    public function testUpdatingProfile(): void
    {
        $profileId = $this->addProfile();
        self::assertEquals(1, $this->updateProfile());

        $updatedProfileData = $this->getUpdatedProfileData();
        $updatedProfile = Model::get($profileId);

        self::assertInstanceOf(Profile::class, $updatedProfile);

        self::assertEquals($profileId, $updatedProfile->getId());
        self::assertEquals($updatedProfileData['email'], $updatedProfile->getEmail());
        self::assertEquals($updatedProfileData['status'], $updatedProfile->getStatus());
        self::assertEquals($updatedProfileData['display_name'], $updatedProfile->getDisplayName());
        self::assertEquals($updatedProfileData['url'], $updatedProfile->getUrl());

        self::assertEquals($profileId, Model::getIdByEmail($updatedProfileData['email']));
    }

    public function testProfileExists(): void
    {
        $profileData = $this->getProfileData();

        self::assertFalse(Model::existsDisplayName($profileData['display_name']));
        self::assertFalse(Model::existsByEmail($profileData['email']));

        $this->addProfile();

        self::assertTrue(Model::existsDisplayName($profileData['display_name']));
        self::assertTrue(Model::existsByEmail($profileData['email']));
    }

    public function testPasswordGetsEncrypted(): void
    {
        $encryptedPassword = Model::encryptPassword('Fork CMS');

        self::assertTrue(password_verify('Fork CMS', $encryptedPassword));
    }

    public function testGettingEncryptedPassword(): void
    {
        $this->addProfile();

        self::assertEquals(
            '$2y$10$1Ev9QQNYZBjdU1ELKjKNqelcV.j2l3CgtVkHl0aMvbNpg1g73S5lC',
            Model::getEncryptedPassword('test@fork-cms.com')
        );
    }

    public function testVerifyingPassword(): void
    {
        $this->addProfile();

        self::assertTrue(Model::verifyPassword('test@fork-cms.com', 'forkcms'));
    }

    public function testSettingSettings(): void
    {
        Model::setSetting(1, 'my_setting', 'My setting\'s value');
        self::assertEquals('My setting\'s value', Model::getSetting(1, 'my_setting'));

        Model::setSetting(1, 'my_setting', 'My updated value');
        self::assertEquals('My updated value', Model::getSetting(1, 'my_setting'));

        Model::setSettings(
            1,
            [
                'my_setting' => 'Another updated value',
                'my_other_setting' => 'A new value',
            ]
        );

        self::assertEquals('Another updated value', Model::getSetting(1, 'my_setting'));
        self::assertEquals('A new value', Model::getSetting(1, 'my_other_setting'));
    }

    public function testGettingSettings(): void
    {
        Model::setSetting(1, 'my_setting', 'My setting\'s value');
        Model::setSetting(1, 'my_array', ['one', 'two', 'banana']);
        Model::setSetting(2, 'someone_elses_setting', 'Someone else\'s setting\'s value');

        $settings = Model::getSettings(1);

        self::assertContains('My setting\'s value', $settings);
        self::assertContains(['one', 'two', 'banana'], $settings);
        self::assertNotContains('Someone else\'s setting\'s value', $settings);
    }

    public function testDeletingSetting(): void
    {
        Model::setSetting(1, 'my_setting', 'My setting\'s value');
        self::assertEquals('My setting\'s value', Model::getSetting(1, 'my_setting'));

        Model::deleteSetting(1, 'my_setting');
        self::assertEquals('', Model::getSetting(1, 'my_setting'));
    }

    public function testGettingId(): void
    {
        $this->addProfile();

        self::assertEquals(1, Model::getIdByEmail('test@fork-cms.com'));

        Model::setSetting(1, 'get_my_id', 'with_a_setting');
        self::assertEquals(1, Model::getIdBySetting('get_my_id', 'with_a_setting'));
    }

    public function testGettingRandomString()
    {
        self::assertNotEmpty(Model::getRandomString());
        self::assertEquals(15, strlen(Model::getRandomString()));
        self::assertEquals(14, strlen(Model::getRandomString(14)));
        self::assertEquals(1, preg_match("#^[0-9]+$#", Model::getRandomString(15, true, false, false, false)));
        self::assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, true, false, false)));
        self::assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, true, true, false)));
        self::assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, true, true, true)));
        self::assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, false, true, false)));
        self::assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, false, true, true)));
        self::assertEquals(0, preg_match("#^[0-9]+$#", Model::getRandomString(15, false, false, false, true)));
        self::assertEquals(1, preg_match("#^[a-z]+$#", Model::getRandomString(15, false, true, false, false)));
        self::assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, true, false, false, false)));
        self::assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, true, false, true, false)));
        self::assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, true, false, true, true)));
        self::assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, false, false, true, false)));
        self::assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, false, false, true, true)));
        self::assertEquals(0, preg_match("#^[a-z]+$#", Model::getRandomString(15, false, false, false, true)));
        self::assertEquals(1, preg_match("#^[A-Z]+$#", Model::getRandomString(15, false, false, true, false)));
        self::assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, true, false, false, false)));
        self::assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, true, true, false, false)));
        self::assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, true, true, false, true)));
        self::assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, false, true, false, false)));
        self::assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, false, true, false, true)));
        self::assertEquals(0, preg_match("#^[A-Z]+$#", Model::getRandomString(15, false, false, false, true)));
        self::assertEquals(
            1,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, false, false, false, true)
            )
        );
        self::assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, true, false, false, false)
            )
        );
        self::assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, true, true, false, false)
            )
        );
        self::assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, true, true, true, false)
            )
        );
        self::assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, false, true, false, false)
            )
        );
        self::assertEquals(
            0,
            preg_match(
                "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
                Model::getRandomString(15, false, true, true, false)
            )
        );
        self::assertEquals(
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
        self::assertEquals('fork-cms', $firstUrl);

        $this->addProfile();

        $secondUrl = Model::getUrl('Fork CMS');
        self::assertEquals('fork-cms-2', $secondUrl);
    }

    public function addProfile(): int
    {
        return Model::insert($this->getProfileData());
    }

    public function updateProfile(): int
    {
        return Model::update(1, $this->getUpdatedProfileData());
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

    public function getUpdatedProfileData(): array
    {
        return [
            'email' => 'test2@fork-cms.com',
            'password' => '$2y$10$1Ev9QQNYZBjdU1ELKjKNqelcV.j2l3CgtVkHl0aMvbNpg1g73S5lC',
            'status' => 'archived',
            'display_name' => 'Fork CMS 2',
            'url' => 'fork-cms-2',
            'registered_on' => '2018-03-05 10:22:34',
            'last_login' => '2018-03-05 10:16:19',
        ];
    }
}
