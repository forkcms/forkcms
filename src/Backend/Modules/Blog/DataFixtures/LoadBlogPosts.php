<?php

namespace Backend\Modules\Blog\DataFixtures;

use Backend\Core\Language\Locale;
use Backend\Modules\Blog\Domain\Category\Category;
use Common\Doctrine\Entity\Meta;
use SpoonDatabase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadBlogPosts
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * LoadBlogCategories constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load(SpoonDatabase $database): void
    {
        $meta = new Meta(
            'Blogpost for functional tests',
            false,
            'Blogpost for functional tests',
            false,
            'Blogpost for functional tests',
            false,
            'blogpost-for-functional-tests',
            false
        );
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
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
            'BlogCategory for tests',
            false,
            'BlogCategory for tests',
            false,
            'BlogCategory for tests',
            false,
            'blogcategory-for-tests',
            false
        );

        $category = new Category(
            Locale::fromString('en'),
            'Blogcategory for functional tests',
            $meta
        );

        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $entityManager->persist($category);
        $entityManager->flush($category);

        return $category;
    }
}
