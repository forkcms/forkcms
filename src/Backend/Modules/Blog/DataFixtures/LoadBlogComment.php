<?php

namespace Backend\Modules\Blog\DataFixtures;

use Backend\Core\Language\Locale;
use Backend\Modules\Blog\Domain\Category\Category;
use Backend\Modules\Blog\Domain\Comment\Comment;
use Common\Doctrine\Entity\Meta;
use SpoonDatabase;
use Symfony\Bundle\FrameworkBundle\Client;

class LoadBlogComment
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function load(SpoonDatabase $database): void
    {
        $this->insertPost($database);

        $comment = new Comment(
            1,
            Locale::fromString('en'),
            'John Doe',
            'john@example.com',
            'This is just a short text, that represents a comment',
            'comment',
            'published',
            'http://example.com',
            null
        );

        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $entityManager->persist($comment);
        $entityManager->flush($comment);
    }

    private function insertPost(SpoonDatabase $database)
    {
        $meta = new Meta(
            'Blogpost for functional tests', false,
            'Blogpost for functional tests', false,
            'Blogpost for functional tests', false,
            'blogpost-for-functional-tests', false
        );

        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $entityManager->persist($meta);
        $entityManager->flush($meta);

        $database->insert(
            'blog_posts',
            [
                'id' => 1,
                'meta_id' => $meta->getId(),
                'category_id' => $this->insertCategory()->getId(),
                'user_id' => 1,
                'language' => 'en',
                'title' => 'Blogpost for functional tests',
                'introduction' => '<p>Lorem ipsum dolor sit amet</p>',
                'text' => '<p>Lorem ipsum dolor sit amet</p>',
                'status' => 'active',
                'publish_on' => '2015-02-23 00:00:00',
                'created_on' => '2015-02-23 00:00:00',
                'edited_on' => '2015-02-23 00:00:00',
                'num_comments' => 0,
            ]
        );

        $database->insert(
            'search_index',
            [
                'module' => 'Blog',
                'other_id' => 1,
                'field' => 'title',
                'value' => 'Blogpost for functional tests',
                'language' => 'en',
                'active' => true,
            ]
        );
    }

    private function insertCategory(): Category
    {
        $meta = new Meta(
            'BlogCategory for tests', false,
            'BlogCategory for tests', false,
            'BlogCategory for tests', false,
            'blogcategory-for-tests', false
        );

        $category = new Category(
            Locale::fromString('en'),
            'Blog category for functional tests',
            $meta
        );

        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $entityManager->persist($category);
        $entityManager->flush($category);

        return $category;
    }
}
