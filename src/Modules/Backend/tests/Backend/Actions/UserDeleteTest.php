<?php

namespace ForkCMS\Modules\Backend\tests\Backend\Actions;

use ForkCMS\Modules\Backend\DataFixtures\UserFixture;
use ForkCMS\Modules\Backend\DataFixtures\UserGroupFixture;
use ForkCMS\Modules\Backend\tests\BackendWebTestCase;

final class UserDeleteTest extends BackendWebTestCase
{
    protected const TEST_URL = '/private/en/backend/user-delete';

    public function testWithoutSubmitRedirectToIndex(): void
    {
        $user = self::loadPage();
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/backend/user-index');
        self::assertDataGridHasLink($user->getEmail());
        self::assertResponseContains('This user doesn\'t exist.');
    }

    public function testSubmittedFormDeletesUser(): void
    {
        self::loadPage('/private/en/backend/user-index');
        self::assertClickOnLink(UserFixture::SUPER_ADMIN_EMAIL, []);
        self::assertCurrentUrlContains('/private/en/backend/user-edit/');
        self::submitForm('Delete');
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/backend/user-index');
        self::assertDataGridHasLink(UserFixture::USER_EMAIL);
        self::assertDataGridNotHasLink(UserFixture::SUPER_ADMIN_EMAIL);
        self::assertResponseContains('The user "Super admin" was deleted.');
    }

    protected static function getClassFixtures(): array
    {
        return [
            new UserGroupFixture(),
            new UserFixture(),
        ];
    }
}
