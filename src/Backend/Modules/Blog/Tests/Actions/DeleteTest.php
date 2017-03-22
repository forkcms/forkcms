<?php

namespace Backend\Modules\Blog\Tests\Action;

use Common\WebTestCase;

class DeleteTest extends WebTestCase
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
        $client->request('GET', '/private/en/blog/delete?id=1');

        // we should get redirected to authentication with a reference to the wanted page
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fblog%2Fdelete%3Fid%3D1',
            $client->getHistory()->current()->getUri()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testDeleteIsAvailableFromTheEditpage()
    {
        $client = static::createClient();
        $this->login();

        $crawler = $client->request('GET', '/private/en/blog/edit?token=1234&id=1');
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );

        $link = $crawler->filter('a:contains("Delete")')->link();
        $client->click($link);

        // we're now on the delete page of the blogpost with id 1
        self::assertContains(
            '/private/en/blog/delete',
            $client->getHistory()->current()->getUri()
        );
        self::assertContains(
            'id=1',
            $client->getHistory()->current()->getUri()
        );

        // we're redirected back to the index page after deletion
        $client->followRedirect();
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '/private/en/blog/index',
            $client->getHistory()->current()->getUri()
        );
        self::assertContains(
            '&report=deleted&var=Blogpost%20for%20functional%20tests',
            $client->getHistory()->current()->getUri()
        );

        // the blogpost should not be available anymore
        self::assertNotContains(
            'Blogpost for functional tests',
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

        $client->request('GET', '/private/en/blog/delete?id=12345678');
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
