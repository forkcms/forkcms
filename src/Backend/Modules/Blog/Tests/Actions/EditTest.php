<?php

namespace Backend\Modules\Blog\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class EditTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/detail_theme?theme=Fork');
    }

    public function testWeCanGoToEditFromTheIndexPage(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $this->login($client);

        $this->assertPageLoadedCorrectly($client, '/private/en/blog/index', [LoadBlogPosts::BLOG_POST_TITLE]);

        $link = $client->getCrawler()->selectLink(LoadBlogPosts::BLOG_POST_TITLE)->link();
        $client->click($link);

        $this->assertIs200($client);
        $this->assertCurrentUrlContains($client, '&id=1');
    }

    public function testEditingOurBlogPost(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $this->login($client);

        $this->assertPageLoadedCorrectly(
            $client,
            '/private/en/blog/edit?id=1',
            ['form method="post" action="/private/en/blog/edit?id=1" id="edit"']
        );

        $form = $client->getCrawler()->selectButton('Publish')->form();

        $newBlogPostTitle = 'Edited blogpost for functional tests';
        $client->setMaxRedirects(1);
        $this->submitEditForm(
            $client,
            $form,
            [
                'title' => $newBlogPostTitle,
            ]
        );

        // we should get a 200 and be redirected to the index page
        $this->assertIs200($client);
        // our url and our page should contain the new title of our blog post
        $this->assertCurrentUrlContains(
            $client,
            '/private/en/blog/index',
            '&id=1&highlight%3Drow=2&var=' . rawurlencode($newBlogPostTitle) . '&report=edited'
        );
        $this->assertResponseHasContent($client->getResponse(), $newBlogPostTitle);
    }

    public function testSubmittingInvalidData(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $this->login($client);

        $this->assertHttpStatusCode200($client, '/private/en/blog/edit?id=1');

        $crawler = $client->getCrawler();

        $form = $crawler->selectButton('Publish')->form();
        $this->submitEditForm(
            $client,
            $form,
            [
                'title' => '',
            ]
        );

        $this->assertIs200($client);
        $this->assertCurrentUrlContains($client, '/private/en/blog/edit');

        // our page shows an overall error message and a specific one
        $this->assertResponseHasContent($client->getResponse(), 'Something went wrong', 'Provide a title.');
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

        $this->assertGetsRedirected($client, '/private/en/blog/edit?id=12345678', '/private/en/blog/index');
        $this->assertCurrentUrlContains($client, 'error=non-existing');
    }
}
