<?php

namespace Backend\Modules\Blog\Tests\Action;

use Common\WebTestCase;

class DeleteTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);
        $this->loadFixtures(
            $client,
            [
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            ]
        );

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/blog/delete?id=1');

        // we should get redirected to authentication with a reference to the wanted page
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fblog%2Fdelete%3Fid%3D1',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testInvalidIdShouldShowAnError(): void
    {
        $client = static::createClient();
        $this->login($client);

        // go to edit page to get a form token
        $crawler = $client->request('GET', '/private/en/blog/edit?token=1234&id=1');
        $token = $crawler->filter('#blog_delete__token')->attr('value');

        // do request
        $client->request('POST', '/private/en/blog/delete', ['blog_delete' => ['_token' => $token, 'id' => 12345678]]);
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

    public function testDeleteIsAvailableFromTheEditpage(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/blog/edit?token=1234&id=1');
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
