<?php

namespace Backend\Modules\Authentication\Tests\Action;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
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
        $client->followRedirects();

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
            'now editing:',
            $client->getResponse()->getContent()
        );
    }

    public function testPagesUserWithCorrectCredentials()
    {
        $client = static::createClient();
        $client->followRedirects();

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
    }
}
