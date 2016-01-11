<?php

namespace Backend\Modules\Blog\Tests\Action;

use Common\WebTestCase;

class DeleteTest extends WebTestCase
{
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
        $this->assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fblog%2Fdelete%3Fid%3D1',
            $client->getHistory()->current()->getUri()
        );
    }
    public function testDeleteIsAvailableFromTheEditpage()
    {
        $client = static::createClient();
        $this->login();

        $crawler = $client->request('GET', '/private/en/blog/edit?token=1234&id=1');
        $this->assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );

        $link = $crawler->selectLink('OK')->link();
        $client->click($link);

        // we're now on the delete page of the blogpost with id 1
        $this->assertContains(
            '/private/en/blog/delete',
            $client->getHistory()->current()->getUri()
        );
        $this->assertContains(
            'id=1',
            $client->getHistory()->current()->getUri()
        );

        // we're redirected back to the index page after deletion
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            '/private/en/blog/index',
            $client->getHistory()->current()->getUri()
        );
        $this->assertContains(
            '&report=deleted&var=Blogpost+for+functional+tests',
            $client->getHistory()->current()->getUri()
        );

        // the blogpost should not be available anymore
        $this->assertNotContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }

    public function testInvalidIdShouldShowAnError()
    {
        $client = static::createClient();
        $this->login();

        $client->request('GET', '/private/en/blog/delete?id=12345678');
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
