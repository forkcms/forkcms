<?php

namespace ForkCMS\Modules\Backend\tests\Backend\Actions;

use ForkCMS\Modules\Backend\tests\BackendWebTestCase;

final class UserAddTest extends BackendWebTestCase
{
    protected const TEST_URL = '/private/en/backend/user-add';

    public function testPageLoads(): void
    {
        self::loginBackendUser();
        self::assertPageLoadedCorrectly(
            self::TEST_URL,
            'Add | users | settings | Fork CMS | Fork CMS',
            [
                'Display name',
                'E-mail',
                'Super admin (grant access to everything)',
                'Enable CMS access for this account.',
                'Short date format',
            ]
        );
        self::assertHasLink('Cancel', '/private/en/backend/user-index');
    }

    public function testEmptyFormShowsValidationErrors(): void
    {
        self::loadPage();
        self::assertEmptyFormSubmission('user', 3);
    }

    public function testValidData(): void
    {
        self::loadPage();

        self::submitForm(
            'Add',
            [
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][displayName]' => 'Jelmer Prins',
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][email]' => 'jelmer.prins',
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][plainTextPassword][first]' => 'I<3ForkCMS',
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][plainTextPassword][second]' => 'I<3ForkCMS',
            ],
            'The password is too short.',
            'Please provide a valid e-mail address.',
        );
    }

    public function testUniqueness(): void
    {
        $user = self::loginBackendUser();
        self::loadPage(loginBackendUser: false);

        self::submitForm(
            'Add',
            [
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][displayName]' => $user->getDisplayName(),
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][email]' => $user->getEmail(),
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][plainTextPassword][first]' => 'IAbsolutely<3ForkCMS',
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][plainTextPassword][second]' => 'IAbsolutely<3ForkCMS',
            ],
            'This e-mailaddress is in use.',
            'This display name is in use.',
        );
    }

    public function testSubmittedFormRedirectsToIndex(): void
    {
        self::loadPage();

        self::submitForm(
            'Add',
            [
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][displayName]' => 'Jelmer Prins',
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][email]' => 'jelmer.prins@fork-cms.com',
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][plainTextPassword][first]' => 'IAbsolutely<3ForkCMS',
                'user[user][ec05aaca240e74a0604d93f9e5a7caef][plainTextPassword][second]' => 'IAbsolutely<3ForkCMS',
            ],
        );
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/backend/user-index');
        self::assertDataGridHasLink('jelmer.prins@fork-cms.com');
        self::assertResponseHasContent('The user "Jelmer Prins" was added.');
    }
}
