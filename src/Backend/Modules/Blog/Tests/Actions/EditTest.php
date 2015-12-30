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
}
