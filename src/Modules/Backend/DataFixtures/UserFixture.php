<?php

namespace ForkCMS\Modules\Backend\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForkCMS\Modules\Backend\Domain\User\Command\CreateUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Extensions\tests\ForkFixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixture extends ForkFixture implements DependentFixtureInterface
{
    public const PLAIN_TEXT_PASSWORD = 'test';
    private const PASSWORD = '$2y$13$YJGHIjorExsQSD9VCzMgBuwi5QS3Zr2tVBisWS4vrcKDin/9BdBQq';
    public const SUPER_ADMIN_REFERENCE = 'user-super-admin';
    public const USER_REFERENCE = 'user-super-admin';

    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $createSuperAdmin = new CreateUser();
        $createSuperAdmin->email = 'super-admin@fork-cms.com';
        $createSuperAdmin->displayName = 'Super admin';
        $createSuperAdmin->superAdmin = true;
        $createSuperAdmin->plainTextPassword = self::PLAIN_TEXT_PASSWORD;
        $createSuperAdmin->userGroups->add($this->getReference(UserGroupFixture::ONLY_DASHBOARD_REFERENCE));
        $superAdmin = User::fromDataTransferObject($createSuperAdmin);
        $superAdmin->setPassword(self::PASSWORD);
        $manager->persist($superAdmin);
        $this->setReference(self::SUPER_ADMIN_REFERENCE, $superAdmin);

        $createUser = new CreateUser();
        $createUser->email = 'user@fork-cms.com';
        $createUser->displayName = 'Normal user';
        $createUser->superAdmin = false;
        $createUser->plainTextPassword = self::PLAIN_TEXT_PASSWORD;
        $createUser->userGroups->add($this->getReference(UserGroupFixture::ONLY_DASHBOARD_REFERENCE));
        $user = User::fromDataTransferObject($createUser);
        $user->setPassword(self::PASSWORD);
        $manager->persist($user);
        $this->setReference(self::USER_REFERENCE, $user);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserGroupFixture::class,
        ];
    }
}
