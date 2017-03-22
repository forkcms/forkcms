<?php

namespace Backend\Modules\Blog\Tests\Action;

use Common\WebTestCase;

class EditTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAuthenticationIsNeeded()
    {
        $this->logout();
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
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fblog%2Fedit%3Fid%3D1',
            $client->getHistory()->current()->getUri()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testWeCanGoToEditFromTheIndexPage()
    {
        $client = static::createClient();
        $this->login();

        $crawler = $client->request('GET', '/private/en/blog/index');
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );

        $link = $crawler->selectLink('Blogpost for functional tests')->link();
        $client->click($link);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '&id=1',
            $client->getHistory()->current()->getUri()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testEditingOurBlogPost()
    {
        $client = static::createClient();
        $this->login();

        $crawler = $client->request('GET', '/private/en/blog/edit?id=1');
        self::assertContains(
            'form method="post" action="/private/en/blog/edit?id=1" id="edit"',
            $client->getResponse()->getContent()
        );

        $form = $crawler->selectButton('Publish')->form();

        $client->setMaxRedirects(1);
        $this->submitEditForm($client, $form, array(
            'title' => 'Edited blogpost for functional tests',
        ));

        // we should get a 200 and be redirected to the index page
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '/private/en/blog/index',
            $client->getHistory()->current()->getUri()
        );

        // our url and our page should contain the new title of our blogpost
        self::assertContains(
            '&report=edited&var=Edited%20blogpost%20for%20functional%20tests&id=1',
            $client->getHistory()->current()->getUri()
        );
        self::assertContains(
            'Edited blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @runInSeparateProcess
     */
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
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '/private/en/blog/edit',
            $client->getHistory()->current()->getUri()
        );

        // our page shows an overal error message and a specific one
        self::assertContains(
            'Something went wrong',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Provide a title.',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testInvalidIdShouldShowAnError()
    {
        $client = static::createClient();
        $this->login();

        $client->request('GET', '/private/en/blog/edit?id=12345678');
        $client->followRedirect();

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '/private/en/blog/index',
            $client->getHistory()->current()->getUri()
        );
        self::assertContains(
            'error=non-existing',
            $client->getHistory()->current()->getUri()
        );
    }
}
