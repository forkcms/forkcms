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
            'Add | Users | Settings | Fork CMS | Fork CMS',
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
        self::assertEmptyFormSubmission('user', 3, 'Add');
    }

    public function testWithInvalidData(): void
    {
        self::loadPage();

        self::submitForm(
            'Add',
            [
                'user[user][tab_Authentication][displayName]' => 'Jelmer Prins',
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
        $user = self::loadPage();

        self::submitForm(
            'Add',
            [
                'user[user][tab_Authentication][displayName]' => $user->getDisplayName(),
                'user[user][tab_Authentication][email]' => $user->getEmail(),
                'user[user][tab_Authentication][plainTextPassword][first]' => 'IAbsolutely<3ForkCMS',
                'user[user][tab_Authentication][plainTextPassword][second]' => 'IAbsolutely<3ForkCMS',
            ],
            'This e-mailaddress is in use.',
            'This display name is in use.',
        );
    }

    public function testSubmittedFormRedirectsToEdit(): void
    {
        self::loadPage();

        self::submitForm(
            'Add',
            [
                'user[user][tab_Authentication][displayName]' => 'Jelmer Prins',
                'user[user][tab_Authentication][email]' => 'jelmer.prins@example.com',
                'user[user][tab_Authentication][plainTextPassword][first]' => 'IAbsolutely<3ForkCMS',
                'user[user][tab_Authentication][plainTextPassword][second]' => 'IAbsolutely<3ForkCMS',
            ],
        );
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/backend/user-edit');
        self::assertDataGridHasLink('jelmer.prins@example.com');
        self::assertResponseContains('The user "Jelmer Prins" was added.');
    }
}
