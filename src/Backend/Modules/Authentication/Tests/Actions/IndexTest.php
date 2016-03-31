<?php

namespace Backend\Modules\Authentication\Tests\Action;

use Common\WebTestCase;
use Backend\Core\Engine\Authentication as Authentication;

class IndexTest extends WebTestCase
{
    /**
     * The authentication class persist the previous user.
     * In practice this situation will almost never occur:
     * Login with one user, log out and subsequently log in
     * with another user without a page reload to reinitialize
     * the application.
     * If the clients could be insulated from eachother, this
     * would not be an issue.
     */
    protected function tearDown()
    {
        Authentication::tearDown();
    }

    public function testPrivateRedirectsToAuthentication()
    {
        $client = static::createClient();
        $client->followRedirects();
        $this->loadFixtures($client);

        $client->request('GET', '/private');
        $this->assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testAuthenticationIndexWorks()
    {
        $client = static::createClient();

        $client->request('GET', '/private/en/authentication');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testPrivateContainsRobotsTag()
    {
        $client = static::createClient();

        $client->request('GET', '/private/en/authentication');
        $this->assertContains(
            '<meta name="robots" content="noindex, nofollow"',
            $client->getResponse()->getContent()
        );
    }

    public function testAuthenticationWithWrongCredentials()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/en/authentication');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('login')->form();
        $this->submitForm($client, $form, array(
            'form' => 'authenticationIndex',
            'backend_email' => 'test@test.com',
            'backend_password' => 'wrong_password',
        ));

        // result should not yet be found
        $this->assertContains(
            'Your e-mail and password combination is incorrect.',
            $client->getResponse()->getContent()
        );
    }

    public function testAuthenticationWithCorrectCredentials()
    {
        $client = static::createClient();
        $client->setMaxRedirects(2);

        $crawler = $client->request('GET', '/private/en/authentication');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('login')->form();
        $this->submitForm($client, $form, array(
            'form' => 'authenticationIndex',
            'backend_email' => 'noreply@fork-cms.com',
            'backend_password' => 'fork',
            'form_token' => $form['form_token']->getValue(),
        ));

        $this->assertContains(
            'Dashboard',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'Pages',
            $client->getResponse()->getContent()
        );

        // logout to get rid of this session
        $client->followRedirects(false);
        $client->request('GET', '/private/en/authentication/logout');
    }

    /**
     * Login as a pages user.
     * This user has the rights to access only the pages module.
     */
    public function testPagesUserWithCorrectCredentials()
    {
        $client = static::createClient();
        $client->setMaxRedirects(2);

        $crawler = $client->request('GET', '/private/en/authentication');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('login')->form();
        $this->submitForm($client, $form, array(
            'form' => 'authenticationIndex',
            'backend_email' => 'pages-user@fork-cms.com',
            'backend_password' => 'fork',
            'form_token' => $form['form_token']->getValue(),
        ));

        $this->assertContains(
            'Recently edited',
            $client->getResponse()->getContent()
        );

        // logout to get rid of this session
        $client->followRedirects(false);
        $client->request('GET', '/private/en/authentication/logout');
    }

    /**
     * Login as a users user.
     * This user only has the rights to access the users edit action.
     * It should enable the user to edit his own user-account.
     */
    public function testUsersUserWithCorrectCredentials()
    {
        $client = static::createClient();
        $client->setMaxRedirects(2);

        $crawler = $client->request('GET', '/private/en/authentication');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('login')->form();
        $this->submitForm($client, $form, array(
            'form' => 'authenticationIndex',
            'backend_email' => 'users-edit-user@fork-cms.com',
            'backend_password' => 'fork',
            'form_token' => $form['form_token']->getValue(),
        ));

        $this->assertContains(
            'Edit user "Users User"',
            $client->getResponse()->getContent()
        );

        // logout to get rid of this session
        $client->followRedirects(false);
        $client->request('GET', '/private/en/authentication/logout');
    }
}
