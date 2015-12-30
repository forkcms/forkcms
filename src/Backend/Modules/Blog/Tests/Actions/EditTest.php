<?php

namespace Backend\Modules\Blog\Tests\Action;

use Common\WebTestCase;
use Backend\Core\Engine\Authentication;

class EditTest extends WebTestCase
{
    public function testAuthenticationIsNeeded()
    {
        Authentication::tearDown();
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            )
        );

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/blog/edit?id=1');

        // we should get redirected to authentication with a reference to the wanted page
        $this->assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fblog%2Fedit%3Fid%3D1',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testWeCanGoToEditFromTheIndexPage()
    {
        $client = static::createClient();
        $this->login();

        $crawler = $client->request('GET', '/private/en/blog/index');
        $this->assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );

        $link = $crawler->selectLink('Blogpost for functional tests')->link();
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            '&id=1',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testEditingOurBlogPost()
    {
        $client = static::createClient();
        $this->login();

        $crawler = $client->request('GET', '/private/en/blog/edit?id=1');
        $this->assertContains(
            'Blog: edit article "Blogpost for functional tests"',
            $client->getResponse()->getContent()
        );

        $form = $crawler->selectButton('Publish')->form();

        $client->setMaxRedirects(1);
        $this->submitEditForm($client, $form, array(
            'title' => 'Edited blogpost for functional tests',
        ));

        // we should get a 200 and be redirected to the index page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            '/private/en/blog/index',
            $client->getHistory()->current()->getUri()
        );

        // our url and our page should contain the new title of our blogpost
        $this->assertContains(
            '&report=edited&var=Edited+blogpost+for+functional+tests&id=1',
            $client->getHistory()->current()->getUri()
        );
        $this->assertContains(
            'Edited blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }

    public function testSubmittingInvalidData()
    {
        $client = static::createClient();
        $this->login();

        $crawler = $client->request('GET', '/private/en/blog/edit?id=1');

        $form = $crawler->selectButton('Publish')->form();
        $this->submitEditForm($client, $form, array(
            'title' => '',
        ));

        // we should get a 200 and be redirected to the index page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            '/private/en/blog/edit',
            $client->getHistory()->current()->getUri()
        );

        // our page shows an overal error message and a specific one
        $this->assertContains(
            'Something went wrong',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'Provide a title.',
            $client->getResponse()->getContent()
        );
    }

    public function testInvalidIdShouldShowAnError()
    {
        $client = static::createClient();
        $this->login();

        $client->request('GET', '/private/en/blog/edit?id=12345678');
        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            '/private/en/blog/index',
            $client->getHistory()->current()->getUri()
        );
        $this->assertContains(
            'error=non-existing',
            $client->getHistory()->current()->getUri()
        );
    }
}
