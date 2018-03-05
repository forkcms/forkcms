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
    }

    public function testGettingId(): void
    {
        $this->addProfile();

        $this->assertEquals(1, Model::getIdByEmail('test@fork-cms.com'));

        Model::setSetting(1, 'get_my_id', 'with_a_setting');
        $this->assertEquals(1, Model::getIdBySetting('get_my_id', 'with_a_setting'));
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
