<?php

namespace Backend\Modules\Blog\DataFixtures;

use Backend\Core\Language\Locale;
use Backend\Modules\Blog\Domain\Category\Category;
use Common\Doctrine\Entity\Meta;
use SpoonDatabase;
use Symfony\Bundle\FrameworkBundle\Client;

class LoadBlogCategories
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
    }
}
