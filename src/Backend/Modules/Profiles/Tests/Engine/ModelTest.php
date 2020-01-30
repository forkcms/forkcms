<?php

namespace Backend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\Engine\Model;
use Backend\Core\Tests\BackendWebTestCase;
use DateTime;

final class ModelTest extends BackendWebTestCase
{
    /** @var int */
    private $expiresOnTimestamp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->expiresOnTimestamp = time() + 60 * 60;
    }

    public function testPasswordGetsEncrypted(): void
    {
        $encryptedPassword = Model::encryptPassword($this->getPassword());

        self::assertTrue(password_verify($this->getPassword(), $encryptedPassword));
    }

    public function testInsertingProfile(): void
    {
        $profileId = $this->addProfile();

        $profileData = $this->getProfileData();
        $addedProfile = Model::get($profileId);

        self::assertEquals($profileId, $addedProfile['id']);
        self::assertEquals($profileData['email'], $addedProfile['email']);
        self::assertEquals($profileData['status'], $addedProfile['status']);
        self::assertEquals($profileData['display_name'], $addedProfile['display_name']);
        self::assertEquals($profileData['url'], $addedProfile['url']);

        $addedProfileByEmail = Model::getByEmail($profileData['email']);

        self::assertEquals($profileId, $addedProfileByEmail['id']);
        self::assertEquals($profileData['email'], $addedProfileByEmail['email']);
        self::assertEquals($profileData['status'], $addedProfileByEmail['status']);
        self::assertEquals($profileData['display_name'], $addedProfileByEmail['display_name']);
        self::assertEquals($profileData['url'], $addedProfileByEmail['url']);
    }

    public function testGettingUrl(): void
    {
        $firstUrl = Model::getUrl($this->getDisplayName());
        self::assertEquals('fork-cms', $firstUrl);

        $this->addProfile();

        $secondUrl = Model::getUrl($this->getDisplayName());
        self::assertEquals('fork-cms-2', $secondUrl);
    }

    public function testIfProfileExists(): void
    {
        $this->addProfile();
        self::assertTrue(Model::exists(1));
        self::assertTrue(Model::existsByEmail('test@fork-cms.com'));
        self::assertTrue(Model::existsDisplayName($this->getDisplayName()));
    }

    public function testUpdatingProfile(): void
    {
        $profileId = $this->addProfile();
        self::assertEquals(1, $this->updateProfile());

        $updatedProfileData = $this->getUpdatedProfileData();
        $updatedProfile = Model::get($profileId);

        self::assertEquals($profileId, $updatedProfile['id']);
        self::assertEquals($updatedProfileData['email'], $updatedProfile['email']);
        self::assertEquals($updatedProfileData['status'], $updatedProfile['status']);
        self::assertEquals($updatedProfileData['display_name'], $updatedProfile['display_name']);
        self::assertEquals($updatedProfileData['url'], $updatedProfile['url']);
    }

    public function testDeletingProfile(): void
    {
        $profileId = $this->addProfile();
        Model::delete($profileId);

        $deletedProfile = Model::get($profileId);

        self::assertEquals('deleted', $deletedProfile['status']);
    }

    public function testSettingSettings(): void
    {
        $profileId = $this->addProfile();

        Model::setSetting($profileId, 'my_setting', 'My setting\'s value');
        self::assertEquals('My setting\'s value', Model::getSetting(1, 'my_setting'));

        Model::setSetting($profileId, 'my_setting', 'My updated value');
        self::assertEquals('My updated value', Model::getSetting(1, 'my_setting'));
    }

    public function testInsertingGroup(): void
    {
        $groupId = $this->addGroup();

        $groupData = $this->getGroupData();
        $addedGroup = Model::getGroup($groupId);

        self::assertEquals($groupId, $addedGroup['id']);
        self::assertEquals($groupData['name'], $addedGroup['name']);

        $groups = Model::getGroups();

        self::assertContains($groupData['name'], $groups);
    }

    public function testUpdatingGroup(): void
    {
        $groupId = $this->addGroup();
        self::assertEquals(1, $this->updateGroup());

        $updatedGroupData = $this->getUpdatedGroupData();
        $updatedGroup = Model::getGroup($groupId);

        self::assertEquals($groupId, $updatedGroup['id']);
        self::assertEquals($updatedGroupData['name'], $updatedGroup['name']);
    }

    public function testIfGroupExists(): void
    {
        $this->addGroup();
        self::assertTrue(Model::existsGroup(1));
        self::assertTrue(Model::existsGroupName('My Fork CMS group'));
    }

    public function testDeletingGroup(): void
    {
        $profileId = $this->addProfile();
        $groupId = $this->addGroup();
        $profileGroupId = $this->addProfileGroup($profileId, $groupId);

        self::assertNotEmpty(Model::getGroup($groupId));
        self::assertNotEmpty(Model::getProfileGroup($profileGroupId));

        Model::deleteGroup($groupId);

        self::assertSame(Model::getGroup($groupId));
        self::assertSame(Model::getProfileGroup($profileGroupId));
    }

    public function testInsertingProfileGroup(): void
    {
        $profileId = $this->addProfile();
        $groupId = $this->addGroup();

        $profileGroupId = $this->addProfileGroup($profileId, $groupId);

        $profileGroupData = $this->getProfileGroupData($profileId, $groupId);
        $addedProfileGroup = Model::getProfileGroup($profileGroupId);

        self::assertEquals($profileGroupId, $addedProfileGroup['id']);
        self::assertEquals($profileGroupData['profile_id'], $addedProfileGroup['profile_id']);
        self::assertEquals($profileGroupData['group_id'], $addedProfileGroup['group_id']);
        self::assertEquals(strtotime($profileGroupData['expires_on'].'.UTC'), $addedProfileGroup['expires_on']);

        self::assertContains(
            [
                'id' => $profileId,
                'group_id' => $groupId,
                'group_name' => 'My Fork CMS group',
                'expires_on' => $profileGroupData['expires_on'],
            ],
            Model::getProfileGroups(1)
        );
    }

    public function testUpdatingProfileGroup(): void
    {
        $groupId = $this->addGroup();
        $profileId = $this->addProfile();

        $profileGroupId = $this->addProfileGroup($profileId, $groupId);
        self::assertEquals(1, $this->updateProfileGroup());

        $updatedProfileGroupData = $this->getUpdatedProfileGroupData();
        $updatedProfileGroup = Model::getProfileGroup($profileGroupId);

        self::assertEquals($profileGroupId, $updatedProfileGroup['id']);
        self::assertEquals($updatedProfileGroupData['profile_id'], $updatedProfileGroup['profile_id']);
        self::assertEquals($updatedProfileGroupData['group_id'], $updatedProfileGroup['group_id']);
        self::assertEquals(strtotime($updatedProfileGroupData['expires_on'] . '.UTC'), $updatedProfileGroup['expires_on']);
    }

    public function testIfProfileGroupExists(): void
    {
        $profileId = $this->addProfile();
        $groupId = $this->addGroup();

        $profileGroupId = $this->addProfileGroup($profileId, $groupId);
        self::assertTrue(Model::existsProfileGroup($profileGroupId));
    }

    public function testDeletingProfileGroup(): void
    {
        $profileId = $this->addProfile();
        $groupId = $this->addGroup();

        $profileGroupId = $this->addProfileGroup($profileId, $groupId);

        self::assertNotEmpty(Model::getProfileGroup($profileGroupId));

        Model::deleteProfileGroup($profileGroupId);

        self::assertSame(Model::getProfileGroup($profileGroupId));
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
            'expires_on' => date('Y-m-d H:i:s', $this->expiresOnTimestamp),
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
            'expires_on' => date('Y-m-d H:i:s', $this->expiresOnTimestamp + 2),
        ];
    }
}
