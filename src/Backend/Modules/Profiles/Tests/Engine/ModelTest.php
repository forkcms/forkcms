<?php

namespace Backend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\Engine\Model;
use Common\WebTestCase;
use DateTime;

final class ModelTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }

        $client = self::createClient();
        $this->loadFixtures($client);
    }

    public function testPasswordGetsEncrypted(): void
    {
        $encryptedPassword = Model::encryptPassword($this->getPassword());

        $this->assertTrue(password_verify($this->getPassword(), $encryptedPassword));
    }

    public function testInsertingProfile(): void
    {
        $profileId = $this->addProfile();

        $profileData = $this->getProfileData();
        $addedProfile = Model::get($profileId);

        $this->assertEquals($profileId, $addedProfile['id']);
        $this->assertEquals($profileData['email'], $addedProfile['email']);
        $this->assertEquals($profileData['status'], $addedProfile['status']);
        $this->assertEquals($profileData['display_name'], $addedProfile['display_name']);
        $this->assertEquals($profileData['url'], $addedProfile['url']);

        $addedProfileByEmail = Model::getByEmail($profileData['email']);

        $this->assertEquals($profileId, $addedProfileByEmail['id']);
        $this->assertEquals($profileData['email'], $addedProfileByEmail['email']);
        $this->assertEquals($profileData['status'], $addedProfileByEmail['status']);
        $this->assertEquals($profileData['display_name'], $addedProfileByEmail['display_name']);
        $this->assertEquals($profileData['url'], $addedProfileByEmail['url']);
    }

    public function testGettingUrl(): void
    {
        $firstUrl = Model::getUrl($this->getDisplayName());
        $this->assertEquals('fork-cms', $firstUrl);

        $this->addProfile();

        $secondUrl = Model::getUrl($this->getDisplayName());
        $this->assertEquals('fork-cms-2', $secondUrl);
    }

    public function testIfProfileExists(): void
    {
        $this->addProfile();
        $this->assertTrue(Model::exists(1));
        $this->assertTrue(Model::existsByEmail('test@fork-cms.com'));
        $this->assertTrue(Model::existsDisplayName($this->getDisplayName()));
    }

    public function testUpdatingProfile(): void
    {
        $profileId = $this->addProfile();
        $this->assertEquals(1, $this->updateProfile());

        $updatedProfileData = $this->getUpdatedProfileData();
        $updatedProfile = Model::get($profileId);

        $this->assertEquals($profileId, $updatedProfile['id']);
        $this->assertEquals($updatedProfileData['email'], $updatedProfile['email']);
        $this->assertEquals($updatedProfileData['status'], $updatedProfile['status']);
        $this->assertEquals($updatedProfileData['display_name'], $updatedProfile['display_name']);
        $this->assertEquals($updatedProfileData['url'], $updatedProfile['url']);
    }

    public function testDeletingProfile(): void
    {
        $profileId = $this->addProfile();
        Model::delete($profileId);

        $deletedProfile = Model::get($profileId);

        $this->assertEquals('deleted', $deletedProfile['status']);
    }

    public function testSettingSettings(): void
    {
        $profileId = $this->addProfile();

        Model::setSetting($profileId, 'my_setting', 'My setting\'s value');
        $this->assertEquals('My setting\'s value', Model::getSetting(1, 'my_setting'));

        Model::setSetting($profileId, 'my_setting', 'My updated value');
        $this->assertEquals('My updated value', Model::getSetting(1, 'my_setting'));
    }

    public function testInsertingGroup(): void
    {
        $groupId = $this->addGroup();

        $groupData = $this->getGroupData();
        $addedGroup = Model::getGroup($groupId);

        $this->assertEquals($groupId, $addedGroup['id']);
        $this->assertEquals($groupData['name'], $addedGroup['name']);

        $groups = Model::getGroups();

        $this->assertContains($groupData['name'], $groups);
    }

    public function testUpdatingGroup(): void
    {
        $groupId = $this->addGroup();
        $this->assertEquals(1, $this->updateGroup());

        $updatedGroupData = $this->getUpdatedGroupData();
        $updatedGroup = Model::getGroup($groupId);

        $this->assertEquals($groupId, $updatedGroup['id']);
        $this->assertEquals($updatedGroupData['name'], $updatedGroup['name']);
    }

    public function testIfGroupExists(): void
    {
        $this->addGroup();
        $this->assertTrue(Model::existsGroup(1));
        $this->assertTrue(Model::existsGroupName('My Fork CMS group'));
    }

    public function testDeletingGroup(): void
    {
        $profileId = $this->addProfile();
        $groupId = $this->addGroup();
        $profileGroupId = $this->addProfileGroup($profileId, $groupId);

        $this->assertNotEmpty(Model::getGroup($groupId));
        $this->assertNotEmpty(Model::getProfileGroup($profileGroupId));

        Model::deleteGroup($groupId);

        $this->assertEmpty(Model::getGroup($groupId));
        $this->assertEmpty(Model::getProfileGroup($profileGroupId));
    }

    public function testInsertingProfileGroup(): void
    {
        $profileId = $this->addProfile();
        $groupId = $this->addGroup();

        $profileGroupId = $this->addProfileGroup($profileId, $groupId);

        $profileGroupData = $this->getProfileGroupData($profileId, $groupId);
        $addedProfileGroup = Model::getProfileGroup($profileGroupId);

        $this->assertEquals($profileGroupId, $addedProfileGroup['id']);
        $this->assertEquals($profileGroupData['profile_id'], $addedProfileGroup['profile_id']);
        $this->assertEquals($profileGroupData['group_id'], $addedProfileGroup['group_id']);
        $this->assertEquals($profileGroupData['expires_on'], strtotime($addedProfileGroup['expires_on']));

        $this->assertContains(
            [
                'id' => $profileId,
                'group_id' => $groupId,
                'group_name' => 'My Fork CMS group',
                'expires_on' => strtotime($profileGroupData['expires_on']),
            ],
            Model::getProfileGroups(1)
        );
    }

    public function testUpdatingProfileGroup(): void
    {
        $groupId = $this->addGroup();
        $profileId = $this->addProfile();

        $profileGroupId = $this->addProfileGroup($profileId, $groupId);
        $this->assertEquals(1, $this->updateProfileGroup());

        $updatedProfileGroupData = $this->getUpdatedProfileGroupData();
        $updatedProfileGroup = Model::getProfileGroup($profileGroupId);

        $this->assertEquals($profileGroupId, $updatedProfileGroup['id']);
        $this->assertEquals($updatedProfileGroupData['profile_id'], $updatedProfileGroup['profile_id']);
        $this->assertEquals($updatedProfileGroupData['group_id'], $updatedProfileGroup['group_id']);
        $this->assertEquals($updatedProfileGroupData['expires_on'], $updatedProfileGroup['expires_on']);
    }

    public function testIfProfileGroupExists(): void
    {
        $profileId = $this->addProfile();
        $groupId = $this->addGroup();

        $profileGroupId = $this->addProfileGroup($profileId, $groupId);
        $this->assertTrue(Model::existsProfileGroup($profileGroupId));
    }

    public function testDeletingProfileGroup(): void
    {
        $profileId = $this->addProfile();
        $groupId = $this->addGroup();

        $profileGroupId = $this->addProfileGroup($profileId, $groupId);

        $this->assertNotEmpty(Model::getProfileGroup($profileGroupId));

        Model::deleteProfileGroup($profileGroupId);

        $this->assertEmpty(Model::getProfileGroup($profileGroupId));
    }

    public function addProfile(): int
    {
        return Model::insert($this->getProfileData());
    }

    public function addGroup(): int
    {
        return Model::insertGroup($this->getGroupData());
    }

    public function addProfileGroup(int $profileId, int $groupId): int
    {
        return Model::insertProfileGroup($this->getProfileGroupData($profileId, $groupId));
    }

    public function updateProfile(): int
    {
        return Model::update(1, $this->getUpdatedProfileData());
    }

    public function updateGroup(): int
    {
        return Model::updateGroup(1, $this->getUpdatedGroupData());
    }

    public function updateProfileGroup(): int
    {
        return Model::updateProfileGroup(1, $this->getUpdatedProfileGroupData());
    }

    public function getPassword(): string
    {
        return 'forkcms';
    }

    public function getDisplayName(): string
    {
        return 'Fork CMS';
    }

    public function getProfileData(): array
    {
        return [
            'email' => 'test@fork-cms.com',
            'password' => password_hash($this->getPassword(), PASSWORD_DEFAULT),
            'status' => 'active',
            'display_name' => $this->getDisplayName(),
            'url' => 'fork-cms',
            'registered_on' => '2018-03-05 09:45:12',
            'last_login' => '1970-01-01 00:00:00',
        ];
    }

    public function getGroupData(): array
    {
        return [
            'name' => 'My Fork CMS group',
        ];
    }

    public function getProfileGroupData(int $profileId, int $groupId): array
    {
        return [
            'profile_id' => $profileId,
            'group_id' => $groupId,
            'starts_on' => date('Y-m-d H:i:s', time()),
            'expires_on' =>  date('Y-m-d H:i:s', time() + 60 * 60),
        ];
    }

    public function getUpdatedProfileData(): array
    {
        return [
            'email' => 'test2@fork-cms.com',
            'password' => password_hash($this->getPassword(), PASSWORD_DEFAULT),
            'status' => 'blocked',
            'display_name' => 'Fork CMS 2',
            'url' => 'fork-cms-2',
            'registered_on' => '2018-03-05 10:22:34',
            'last_login' => '2018-03-05 10:16:19',
        ];
    }

    public function getUpdatedGroupData(): array
    {
        return [
            'name' => 'My updated Fork CMS group',
        ];
    }

    public function getUpdatedProfileGroupData(): array
    {
        return [
            'profile_id' => 1,
            'group_id' => 1,
            'expires_on' => date('Y-m-d H:i:s', time() + 60 * 60),
        ];
    }
}
