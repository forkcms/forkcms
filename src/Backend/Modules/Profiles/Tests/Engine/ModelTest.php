<?php

namespace Backend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\DataFixtures\LoadProfilesGroup;
use Backend\Modules\Profiles\DataFixtures\LoadProfilesGroupData;
use Backend\Modules\Profiles\DataFixtures\LoadProfilesProfile;
use Backend\Modules\Profiles\Engine\Model;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

final class ModelTest extends BackendWebTestCase
{
    public function testPasswordGetsEncrypted(): void
    {
        $encryptedPassword = Model::encryptPassword(LoadProfilesProfile::PROFILES_PROFILE_PASSWORD);

        self::assertTrue(password_verify(LoadProfilesProfile::PROFILES_PROFILE_PASSWORD, $encryptedPassword));
    }

    public function testInsertingProfile(): void
    {
        $profileData = LoadProfilesProfile::getProfileArray(
            LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DATA
        );
        $profileId = Model::insert($profileData);
        $addedProfile = Model::get($profileId);

        self::assertEquals($profileId, $addedProfile['id']);
        self::assertEquals($profileData['email'], $addedProfile['email']);
        self::assertEquals($profileData['status'], $addedProfile['status']);
        self::assertEquals($profileData['displayName'], $addedProfile['display_name']);
        self::assertEquals($profileData['url'], $addedProfile['url']);

        $addedProfileByEmail = Model::getByEmail($profileData['email']);

        self::assertEquals($profileId, $addedProfileByEmail['id']);
        self::assertEquals($profileData['email'], $addedProfileByEmail['email']);
        self::assertEquals($profileData['status'], $addedProfileByEmail['status']);
        self::assertEquals($profileData['displayName'], $addedProfileByEmail['display_name']);
        self::assertEquals($profileData['url'], $addedProfileByEmail['url']);
    }

    public function testGettingUrl(Client $client): void
    {
        $firstUrl = Model::getUrl(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_URL, $firstUrl);

        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        $secondUrl = Model::getUrl(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME);
        self::assertEquals(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_URL . '-2', $secondUrl);
    }

    public function testIfProfileExists(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        self::assertTrue(Model::exists(LoadProfilesProfile::getProfileActiveId()));
        self::assertTrue(Model::existsByEmail(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL));
        self::assertTrue(Model::existsDisplayName(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME));
    }

    public function testUpdatingProfile(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        $updatedProfileData = [
            'email' => 'test2@fork-cms.com',
            'password' => Model::encryptPassword(LoadProfilesProfile::PROFILES_PROFILE_PASSWORD),
            'status' => 'blocked',
            'display_name' => 'Fork CMS 2',
            'url' => 'fork-cms-2',
            'registered_on' => '2018-03-05 10:22:34',
            'last_login' => '2018-03-05 10:16:19',
        ];
        $profileId = LoadProfilesProfile::getProfileActiveId();

        self::assertEquals(1, Model::update($profileId, $updatedProfileData));

        $updatedProfile = Model::get($profileId);

        self::assertEquals($profileId, $updatedProfile['id']);
        self::assertEquals($updatedProfileData['email'], $updatedProfile['email']);
        self::assertEquals($updatedProfileData['status'], $updatedProfile['status']);
        self::assertEquals($updatedProfileData['display_name'], $updatedProfile['display_name']);
        self::assertEquals($updatedProfileData['url'], $updatedProfile['url']);
    }

    public function testDeletingProfile(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);
        Model::delete(LoadProfilesProfile::getProfileActiveId());
        $deletedProfile = Model::get(LoadProfilesProfile::getProfileActiveId());

        self::assertEquals('deleted', $deletedProfile['status']);
    }

    public function testSettingSettings(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesProfile::class]);
        $profileId = LoadProfilesProfile::getProfileActiveId();

        Model::setSetting($profileId, 'my_setting', 'My setting\'s value');
        self::assertEquals('My setting\'s value', Model::getSetting($profileId, 'my_setting'));

        Model::setSetting($profileId, 'my_setting', 'My updated value');
        self::assertEquals('My updated value', Model::getSetting($profileId, 'my_setting'));
    }

    public function testInsertingGroup(Client $client): void
    {
        $groupData = LoadProfilesGroup::PROFILES_GROUP_DATA;
        $groupId = Model::insertGroup($groupData);

        $addedGroup = Model::getGroup($groupId);

        self::assertEquals($groupId, $addedGroup['id']);
        self::assertEquals($groupData['name'], $addedGroup['name']);

        $groups = Model::getGroups();

        self::assertContains($groupData['name'], $groups);
    }

    public function testUpdatingGroup(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesGroup::class]);
        $updatedGroupData = [
            'name' => 'My updated Fork CMS group',
        ];
        $groupId = LoadProfilesGroup::getGroupId();
        self::assertEquals(1, Model::updateGroup($groupId, $updatedGroupData));

        $updatedGroup = Model::getGroup($groupId);

        self::assertEquals($groupId, $updatedGroup['id']);
        self::assertEquals($updatedGroupData['name'], $updatedGroup['name']);
    }

    public function testIfGroupExists(Client $client): void
    {
        $this->loadFixtures($client, [LoadProfilesGroup::class]);
        self::assertTrue(Model::existsGroup(LoadProfilesGroup::getGroupId()));
        self::assertTrue(Model::existsGroupName(LoadProfilesGroup::PROFILES_GROUP_NAME));
    }

    public function testDeletingGroup(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadProfilesProfile::class,
                LoadProfilesGroup::class,
                LoadProfilesGroupData::class,
            ]
        );

        self::assertNotEmpty(Model::getGroup(LoadProfilesGroup::getGroupId()));
        self::assertNotEmpty(Model::getProfileGroup(LoadProfilesGroupData::getId()));

        Model::deleteGroup(LoadProfilesGroup::getGroupId());

        self::assertEmpty(Model::getGroup(LoadProfilesGroup::getGroupId()));
        self::assertEmpty(Model::getProfileGroup(LoadProfilesGroupData::getId()));
    }

    public function testInsertingProfileGroup(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadProfilesProfile::class,
                LoadProfilesGroup::class,
            ]
        );

        $profileGroupData = [
            'profile_id' => LoadProfilesProfile::getProfileActiveId(),
            'group_id' => LoadProfilesGroup::getGroupId(),
            'starts_on' => date('Y-m-d H:i:s', LoadProfilesGroupData::getStartsOnTimestamp()),
            'expires_on' => date('Y-m-d H:i:s', LoadProfilesGroupData::getExpiresOnTimestamp()),
        ];

        $profileGroupId = Model::insertProfileGroup($profileGroupData);
        $addedProfileGroup = Model::getProfileGroup($profileGroupId);

        self::assertEquals($profileGroupId, $addedProfileGroup['id']);
        self::assertEquals($profileGroupData['profile_id'], $addedProfileGroup['profile_id']);
        self::assertEquals($profileGroupData['group_id'], $addedProfileGroup['group_id']);
        self::assertEquals(strtotime($profileGroupData['expires_on'] . '.UTC'), $addedProfileGroup['expires_on']);

        self::assertContains(
            [
                'id' => $profileGroupId,
                'group_id' => LoadProfilesGroup::getGroupId(),
                'group_name' => LoadProfilesGroup::PROFILES_GROUP_NAME,
                'expires_on' => strtotime($profileGroupData['expires_on'] . '.UTC'),
            ],
            Model::getProfileGroups(LoadProfilesProfile::getProfileActiveId())
        );
    }

    public function testUpdatingProfileGroup(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadProfilesProfile::class,
                LoadProfilesGroup::class,
                LoadProfilesGroupData::class,
            ]
        );
        $updatedProfileGroupData = [
            'profile_id' => LoadProfilesProfile::getProfileActiveId(),
            'group_id' => LoadProfilesGroup::getGroupId(),
            'expires_on' => LoadProfilesGroupData::getExpiresOnTimestamp() + 2,
        ];

        self::assertEquals(1, Model::updateProfileGroup(LoadProfilesGroupData::getId(), $updatedProfileGroupData));


        $updatedProfileGroup = Model::getProfileGroup(LoadProfilesGroupData::getId());

        self::assertEquals(LoadProfilesGroupData::getId(), $updatedProfileGroup['id']);

        self::assertEquals($updatedProfileGroupData['profile_id'], $updatedProfileGroup['profile_id']);
        self::assertEquals($updatedProfileGroupData['group_id'], $updatedProfileGroup['group_id']);

        self::assertEquals(
            $updatedProfileGroupData['expires_on'],
            $updatedProfileGroup['expires_on']
        );
    }

    public function testIfProfileGroupExists(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadProfilesProfile::class,
                LoadProfilesGroup::class,
                LoadProfilesGroupData::class,
            ]
        );

        self::assertTrue(Model::existsProfileGroup(LoadProfilesGroupData::getId()));
    }

    public function testDeletingProfileGroup(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadProfilesProfile::class,
                LoadProfilesGroup::class,
                LoadProfilesGroupData::class,
            ]
        );

        $profileGroupId = LoadProfilesGroupData::getId();

        self::assertNotEmpty(Model::getProfileGroup($profileGroupId));
        Model::deleteProfileGroup($profileGroupId);
        self::assertEmpty(Model::getProfileGroup($profileGroupId));
    }
}
