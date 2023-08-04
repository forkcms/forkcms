<?php

namespace Backend\Modules\Blog\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Symfony\Bundle\FrameworkBundle\Client;

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
        self::assertAuthenticationIsNeeded($client, '/private/en/blog/delete?id=1');
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
        self::assertHttpStatusCode200($client, '/private/en/blog/edit?token=1234&id=1');
        $token = $client->getCrawler()->filter('#blog_delete__token')->attr('value');

        self::assertGetsRedirected(
            $client,
            '/private/en/blog/delete',
            '/private/en/blog/index',
            'POST',
            ['blog_delete' => ['_token' => $token, 'id' => 12345678]]
        );

        self::assertCurrentUrlContains($client, 'error=non-existing');
    }

    public function testDeleteIsAvailableFromTheEditPage(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $this->login($client);
        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/blog/edit?id=1',
            [LoadBlogPosts::BLOG_POST_TITLE]
        );

        $form = $this->getFormForSubmitButton($client, 'Delete', '#confirmDelete');
        $client->submit($form);

        // we're now on the delete page of the blogpost with id 1
        self::assertCurrentUrlContains($client, '/private/en/blog/delete');
        self::assertEquals(
            1,
            $client->getRequest()->request->get('blog_delete')['id']
        );

        // we're redirected back to the index page after deletion
        $client->followRedirect();
        self::assertIs200($client);
        self::assertCurrentUrlContains(
            $client,
            '/private/en/blog/index',
            'report=deleted&var=' . urlencode(LoadBlogPosts::BLOG_POST_TITLE)
        );

        // the blog post should not be available anymore
        self::assertResponseDoesNotHaveContent($client->getResponse(), LoadBlogPosts::BLOG_POST_TITLE);
    }
}
