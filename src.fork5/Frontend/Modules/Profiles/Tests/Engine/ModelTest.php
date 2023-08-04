<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\DataFixtures\LoadProfilesProfile;
use Frontend\Modules\Profiles\Engine\Model;
use Common\WebTestCase;
use Frontend\Core\Tests\FrontendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

final class ModelTest extends FrontendWebTestCase
{
    public function testInsertingProfile(): void
    {
        $profileData = LoadProfilesProfile::getProfileArray(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DATA);
        $profileId = Model::insert($profileData);
        $addedProfile = Model::get($profileId);

        self::assertEquals($profileId, $addedProfile->getId());
        self::assertEquals($profileData['email'], $addedProfile->getEmail());
        self::assertEquals($profileData['status'], $addedProfile->getStatus());
        self::assertEquals($profileData['display_name'], $addedProfile->getDisplayName());
        self::assertEquals($profileData['url'], $addedProfile->getUrl());

        self::assertEquals($profileId, Model::getIdByEmail($profileData['email']));
    }

    public function testUpdatingProfile(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);
        $profileId = LoadProfilesProfile::getProfileActiveId();

        $updatedProfileData = LoadProfilesProfile::getProfileArray(
            [
                'email' => 'test2@fork-cms.com',
                'status' => 'archived',
                'display_name' => 'Fork CMS 2',
                'url' => 'fork-cms-2',
            ]
        );
        self::assertEquals(
            LoadProfilesProfile::getProfileActiveId(),
            Model::update($profileId, $updatedProfileData)
        );

        $updatedProfile = Model::get($profileId);

        self::assertEquals($profileId, $updatedProfile->getId());
        self::assertEquals($updatedProfileData['email'], $updatedProfile->getEmail());
        self::assertEquals($updatedProfileData['status'], $updatedProfile->getStatus());
        self::assertEquals($updatedProfileData['display_name'], $updatedProfile->getDisplayName());
        self::assertEquals($updatedProfileData['url'], $updatedProfile->getUrl());

        self::assertEquals($profileId, Model::getIdByEmail($updatedProfileData['email']));
    }

    public function testProfileExists(Client $client): void
    {
        self::assertFalse(Model::existsDisplayName(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME));
        self::assertFalse(Model::existsByEmail(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL));

        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        self::assertTrue(Model::existsDisplayName(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME));
        self::assertTrue(Model::existsByEmail(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL));
    }

    public function testPasswordGetsEncrypted(): void
    {
        $encryptedPassword = Model::encryptPassword(LoadProfilesProfile::PROFILES_PROFILE_PASSWORD);

        self::assertTrue(password_verify(LoadProfilesProfile::PROFILES_PROFILE_PASSWORD, $encryptedPassword));
    }

    public function testGettingEncryptedPassword(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        self::assertTrue(
            password_verify(
                LoadProfilesProfile::PROFILES_PROFILE_PASSWORD,
                Model::getEncryptedPassword(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL)
            )
        );
    }

    public function testVerifyingPassword(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        self::assertTrue(
            Model::verifyPassword(
                LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL,
                LoadProfilesProfile::PROFILES_PROFILE_PASSWORD
            )
        );
    }

    public function testSettingSettings(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);
        $id = LoadProfilesProfile::getProfileActiveId();

        Model::setSetting($id, 'my_setting', 'My setting\'s value');
        self::assertEquals('My setting\'s value', Model::getSetting($id, 'my_setting'));

        Model::setSetting($id, 'my_setting', 'My updated value');
        self::assertEquals('My updated value', Model::getSetting($id, 'my_setting'));

        Model::setSettings(
            $id,
            [
                'my_setting' => 'Another updated value',
                'my_other_setting' => 'A new value',
            ]
        );

        self::assertEquals('Another updated value', Model::getSetting($id, 'my_setting'));
        self::assertEquals('A new value', Model::getSetting($id, 'my_other_setting'));
    }

    public function testGettingSettings(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        $activeId = LoadProfilesProfile::getProfileActiveId();
        $inactiveId = LoadProfilesProfile::getProfileInactiveId();

        Model::setSetting($activeId, 'my_setting', 'My setting\'s value');
        Model::setSetting($activeId, 'my_array', ['one', 'two', 'banana']);
        Model::setSetting($inactiveId, 'someone_elses_setting', 'Someone else\'s setting\'s value');

        $settings = Model::getSettings($activeId);

        self::assertContains('My setting\'s value', $settings);
        self::assertContains(['one', 'two', 'banana'], $settings);
        self::assertNotContains('Someone else\'s setting\'s value', $settings);
    }

    public function testDeletingSetting(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);
        $activeId = LoadProfilesProfile::getProfileActiveId();

        Model::setSetting($activeId, 'my_setting', 'My setting\'s value');
        self::assertEquals('My setting\'s value', Model::getSetting($activeId, 'my_setting'));

        Model::deleteSetting($activeId, 'my_setting');
        self::assertEquals('', Model::getSetting($activeId, 'my_setting'));
    }

    public function testGettingId(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);
        $activeId = LoadProfilesProfile::getProfileActiveId();

        self::assertEquals($activeId, Model::getIdByEmail(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL));

        Model::setSetting($activeId, 'get_my_id', 'with_a_setting');
        self::assertEquals($activeId, Model::getIdBySetting('get_my_id', 'with_a_setting'));
    }

    public function testGettingRandomString(): void
    {
        self::assertNotEmpty(Model::getRandomString());
        self::assertEquals(15, strlen(Model::getRandomString()));
        self::assertEquals(14, strlen(Model::getRandomString(14)));
        self::assertRegExp('#^[0-9]+$#', Model::getRandomString(15, true, false, false, false));
        self::assertNotRegExp('#^[0-9]+$#', Model::getRandomString(15, false, true, false, false));
        self::assertNotRegExp('#^[0-9]+$#', Model::getRandomString(15, false, true, true, false));
        self::assertNotRegExp('#^[0-9]+$#', Model::getRandomString(15, false, true, true, true));
        self::assertNotRegExp('#^[0-9]+$#', Model::getRandomString(15, false, false, true, false));
        self::assertNotRegExp('#^[0-9]+$#', Model::getRandomString(15, false, false, true, true));
        self::assertNotRegExp('#^[0-9]+$#', Model::getRandomString(15, false, false, false, true));
        self::assertRegExp('#^[a-z]+$#', Model::getRandomString(15, false, true, false, false));
        self::assertNotRegExp('#^[a-z]+$#', Model::getRandomString(15, true, false, false, false));
        self::assertNotRegExp('#^[a-z]+$#', Model::getRandomString(15, true, false, true, false));
        self::assertNotRegExp('#^[a-z]+$#', Model::getRandomString(15, true, false, true, true));
        self::assertNotRegExp('#^[a-z]+$#', Model::getRandomString(15, false, false, true, false));
        self::assertNotRegExp('#^[a-z]+$#', Model::getRandomString(15, false, false, true, true));
        self::assertNotRegExp('#^[a-z]+$#', Model::getRandomString(15, false, false, false, true));
        self::assertRegExp('#^[A-Z]+$#', Model::getRandomString(15, false, false, true, false));
        self::assertNotRegExp('#^[A-Z]+$#', Model::getRandomString(15, true, false, false, false));
        self::assertNotRegExp('#^[A-Z]+$#', Model::getRandomString(15, true, true, false, false));
        self::assertNotRegExp('#^[A-Z]+$#', Model::getRandomString(15, true, true, false, true));
        self::assertNotRegExp('#^[A-Z]+$#', Model::getRandomString(15, false, true, false, false));
        self::assertNotRegExp('#^[A-Z]+$#', Model::getRandomString(15, false, true, false, true));
        self::assertNotRegExp('#^[A-Z]+$#', Model::getRandomString(15, false, false, false, true));
        self::assertRegExp(
            "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
            Model::getRandomString(15, false, false, false, true)
        );
        self::assertNotRegExp(
            "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
            Model::getRandomString(15, true, false, false, false)
        );
        self::assertNotRegExp(
            "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
            Model::getRandomString(15, true, true, false, false)
        );
        self::assertNotRegExp(
            "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
            Model::getRandomString(15, true, true, true, false)
        );
        self::assertNotRegExp(
            "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
            Model::getRandomString(15, false, true, false, false)
        );
        self::assertNotRegExp(
            "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
            Model::getRandomString(15, false, true, true, false)
        );
        self::assertNotRegExp(
            "#^[\-\_\.\:\;\,\?\!\@\#\&\=\)\(\[\]\{\}\*\+\%\$]+$#",
            Model::getRandomString(15, false, false, true, false)
        );
    }

    public function testGettingUrl(Client $client): void
    {
        $firstUrl = Model::getUrl(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_URL, $firstUrl);

        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        $secondUrl = Model::getUrl(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_URL . '-2', $secondUrl);
    }
}
