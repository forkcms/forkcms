<?php

namespace Backend\Modules\Authentication\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class IndexTest extends BackendWebTestCase
{
    public function testPrivateRedirectsToAuthentication(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private');
    }

    public function testAuthenticationIndexWorks(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/authentication',
            ['title="Log in"  name="login" type="submit"']
        );
    }

    public function testPrivateContainsRobotsTag(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/authentication',
            ['<meta name="robots" content="noindex, nofollow"']
        );
    }

    public function testAuthenticationWithWrongCredentials(): void
    {
        $client = static::createClient();

        self::assertHttpStatusCode200($client, '/private/en/authentication');

        $response = $this->submitLoginForm($client, 'test@test.com', 'wrong_password');

        self::assertResponseHasContent($response, 'Your e-mail and password combination is incorrect.');
    }

    public function testAuthenticationWithCorrectCredentials(Client $client): void
    {
        $client->setMaxRedirects(2);

        self::assertHttpStatusCode200($client, '/private/en/authentication');
        $response = $this->submitLoginForm($client, 'noreply@fork-cms.com');

        self::assertResponseHasContent($response, 'Dashboard', 'Pages');
    }

    /**
     * Login as a pages user.
     * This user has the rights to access only the pages module.
     */
    public function testPagesUserWithCorrectCredentials(Client $client): void
    {
        $client->setMaxRedirects(2);

        self::assertHttpStatusCode200($client, '/private/en/authentication');
        $response = $this->submitLoginForm($client, 'pages-user@fork-cms.com');

        self::assertResponseHasContent($response, 'Now editing');
    }

    /**
     * Login as a users user.
     * This user only has the rights to access the users edit action.
     * It should enable the user to edit his own user-account.
     */
    public function testUsersUserWithCorrectCredentials(Client $client): void
    {
        $client->setMaxRedirects(2);

        self::assertHttpStatusCode200($client, '/private/en/authentication');
        $response = $this->submitLoginForm($client, 'users-edit-user@fork-cms.com');

        self::assertResponseHasContent($response, 'Edit profile');
    }

    private function submitLoginForm(Client $client, string $email, string $password = 'fork'): Response
    {
        $form = $this->getFormForSubmitButton($client, 'Log in');
        $this->submitForm(
            $client,
            $form,
            [
                'form' => 'authenticationIndex',
                'backend_email' => $email,
                'backend_password' => $password,
                'form_token' => $form['form_token']->getValue(),
            ]
        );

        return $client->getResponse();
    }
}
