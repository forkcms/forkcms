<?php

namespace ForkCMS\Modules\Backend\tests\Backend\Actions;

use ForkCMS\Modules\Backend\DataFixtures\UserFixture;
use ForkCMS\Modules\Backend\DataFixtures\UserGroupFixture;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Backend\tests\BackendWebTestCase;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;

final class UserEditTest extends BackendWebTestCase
{
    protected const TEST_URL = '/private/en/backend/user-edit/1';

    public function testPageLoads(): void
    {
        $user = self::loginBackendUser();
        self::assertPageLoadedCorrectly(
            self::TEST_URL,
            $user->getDisplayName() . ' | Edit | Users | Settings | Fork CMS | Fork CMS',
            [
                'Display name',
                'E-mail',
                'Super admin (grant access to everything)',
                'Enable CMS access for this account.',
                'Short date format',
                $user->getDisplayName(),
                $user->getEmail(),
            ]
        );

        self::assertHasLink('Users', '/private/en/backend/user-index');
    }

    public function testEditWithoutChanges(): void
    {
        $user = self::loadPage();
        self::assertEmptyFormSubmission('user', 0, 'Save');
        self::assertCurrentUrlEndsWith('/private/en/backend/user-index');
        self::assertDataGridHasLink($user->getEmail());
        self::assertResponseContains('The settings for "' . $user->getDisplayName() . '" were saved.');
    }

    public function testWithInvalidData(): void
    {
        self::loadPage();

        self::submitForm(
            'Save',
            [
                'user[user][tab_Authentication][email]' => 'jelmer.prins',
                'user[user][tab_Authentication][plainTextPassword][first]' => 'I<3ForkCMS',
                'user[user][tab_Authentication][plainTextPassword][second]' => 'I<3ForkCMS',
            ],
            'The password is too short.',
            'Please provide a valid e-mail address.',
        );
    }

    public function testUniqueness(): void
    {
        self::loadPage();

        self::submitForm(
            'Save',
            [
                'user[user][tab_Authentication][displayName]' => 'Super Admin',
                'user[user][tab_Authentication][email]' => UserFixture::SUPER_ADMIN_EMAIL,
            ],
            'This e-mailaddress is in use.',
            'This display name is in use.',
        );
    }

    public function testSubmittedFormRedirectsToIndex(): void
    {
        self::loadPage();

        self::submitForm(
            'Save',
            [
                'user[user][tab_Authentication][displayName]' => 'Jelmer Prins',
                'user[user][tab_Authentication][email]' => 'jelmer.prins@example.com',
                'user[user][tab_Authentication][plainTextPassword][first]' => 'IAbsolutely<3ForkCMS',
                'user[user][tab_Authentication][plainTextPassword][second]' => 'IAbsolutely<3ForkCMS',
            ],
        );
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/backend/user-index');
        self::assertDataGridHasLink('jelmer.prins@example.com');
        self::assertResponseContains('The settings for "Jelmer Prins" were saved.');
    }

    public function testEnableTwoFactorAuthenticationVisible(): void
    {
        /** @var ActionServices $actionServices */
        $actionServices = self::getContainer()->get(ActionServices::class);
        $actionServices->moduleSettings->set(ModuleName::backend(), '2fa_enabled', true);

        self::loadPage();

        self::assertResponseContains('data-role="enable-two-factor-authorization-button"');
    }

    public function testEnableTwoFactorAuthenticationNotVisible(): void
    {
        /** @var ActionServices $actionServices */
        $actionServices = self::getContainer()->get(ActionServices::class);
        $actionServices->moduleSettings->set(ModuleName::backend(), '2fa_enabled', false);

        self::loadPage();

        self::assertResponseDoesNotHaveContent('data-role="enable-two-factor-authorization-button"');
    }

    protected static function getClassFixtures(): array
    {
        return [
            new UserGroupFixture(),
            new UserFixture(),
        ];
    }
}
