<?php

namespace ForkCMS\Modules\Backend\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use ForkCMS\Modules\Backend\Backend\Actions\Dashboard;
use ForkCMS\Modules\Backend\Domain\UserGroup\Command\CreateUserGroup;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use ForkCMS\Modules\Extensions\tests\ForkFixture;

final class UserGroupFixture extends ForkFixture
{
    public const ONLY_DASHBOARD_REFERENCE = 'user-group-only-dasbboard';

    public function load(ObjectManager $manager): void
    {
        $createUserGroup = new CreateUserGroup();
        $createUserGroup->name = 'Super admin';
        $createUserGroup->actions = [Dashboard::class];
        $userGroup = UserGroup::fromDataTransferObject($createUserGroup);
        $this->setReference(self::ONLY_DASHBOARD_REFERENCE, $userGroup);
        $manager->persist($userGroup);
        $manager->flush();
    }
}
