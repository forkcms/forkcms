<?php

namespace Backend\Modules\Blog\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class DeleteTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );
        $this->assertAuthenticationIsNeeded($client, '/private/en/blog/delete?id=1');
    }

    public function testInvalidIdShouldShowAnError(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $this->login($client);

        // go to edit page to get a form token
        $crawler = $client->request('GET', '/private/en/blog/edit?token=1234&id=1');
        $token = $crawler->filter('#blog_delete__token')->attr('value');

        $this->assertGetsRedirected(
            $client,
            '/private/en/blog/delete',
            '/private/en/blog/index',
            'POST',
            ['blog_delete' => ['_token' => $token, 'id' => 12345678]]
        );

        self::assertContains(
            'error=non-existing',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testDeleteIsAvailableFromTheEditpage(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/blog/edit?id=1');
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );

        $form = $crawler->filter('#confirmDelete')->selectButton('Delete')->form();
        $client->submit($form);

        // we're now on the delete page of the blogpost with id 1
        self::assertContains(
            '/private/en/blog/delete',
            $client->getHistory()->current()->getUri()
        );
        self::assertEquals(
            1,
            $client->getRequest()->request->get('blog_delete')['id']
        );

        // we're redirected back to the index page after deletion
        $client->followRedirect();
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '/private/en/blog/index',
            $client->getHistory()->current()->getUri()
        );
        self::assertContains(
            'report=deleted&var=Blogpost+for+functional+tests',
            $client->getHistory()->current()->getUri()
        );

        // the blogpost should not be available anymore
        self::assertNotContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }
}
