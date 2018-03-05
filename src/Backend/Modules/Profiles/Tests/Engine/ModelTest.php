<?php

namespace Backend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\Engine\Model;
use Common\WebTestCase;

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

    public function addProfile(): int
    {
        return Model::insert($this->getProfileData());
    }

    public function updateProfile(): int
    {
        return Model::update(1, $this->getUpdatedProfileData());
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
            'password' => '$2y$10$1Ev9QQNYZBjdU1ELKjKNqelcV.j2l3CgtVkHl0aMvbNpg1g73S5lC',
            'status' => 'active',
            'display_name' => $this->getDisplayName(),
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
