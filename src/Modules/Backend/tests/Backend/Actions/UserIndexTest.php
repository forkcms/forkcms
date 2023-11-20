<?php

use ForkCMS\Modules\Backend\DataFixtures\UserFixture;
use ForkCMS\Modules\Backend\DataFixtures\UserGroupFixture;
use ForkCMS\Modules\Backend\tests\BackendWebTestCase;

final class UserIndexTest extends BackendWebTestCase
{
    protected const TEST_URL = '/private/en/backend/user-index';

    public function testPageLoads(): void
    {
        self::loginBackendUser();
        self::assertPageLoadedCorrectly(
            self::TEST_URL,
            'Users | Settings | Fork CMS | Fork CMS',
            ['Display name', 'E-mail', 'Super admin', 'Normal user']
        );
        self::assertHasLink('Add', '/private/en/backend/user-add');
    }

    public function testDataGrid(): void
    {
        $user = self::loadPage();

        self::assertDataGridHasLink($user->getEmail(), '/private/en/backend/user-edit/' . $user->getId());
        self::assertDataGridHasLink(UserFixture::USER_EMAIL);
        self::assertDataGridHasLink(UserFixture::SUPER_ADMIN_EMAIL);
        self::assertDataGridNotHasLink('demo@example.com');
        self::filterDataGrid('User.email', UserFixture::USER_EMAIL);
        self::assertDataGridHasLink(UserFixture::USER_EMAIL);
        self::assertDataGridNotHasLink(UserFixture::SUPER_ADMIN_EMAIL);
        self::assertDataGridNotHasLink('demo@example.com');
        self::assertDataGridNotHasLink($user->getEmail());
        self::filterDataGrid('User.email', $user->getEmail());
        self::filterDataGrid('User.displayName', 'demo');
        self::assertDataGridIsEmpty();
        self::filterDataGrid('User.displayName', $user->getDisplayName());
        self::assertDataGridHasLink($user->getEmail(), '/private/en/backend/user-edit/' . $user->getId());
    }

    protected static function getClassFixtures(): array
    {
        return [
            new UserGroupFixture(),
            new UserFixture(),
        ];
    }
}
