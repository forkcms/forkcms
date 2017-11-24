<?php

namespace Backend\Modules\Blog\DataFixtures;

use Backend\Core\Language\Locale;
use Backend\Modules\Blog\Domain\Category\Category;
use Common\Doctrine\Entity\Meta;
use SpoonDatabase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadBlogCategories
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
            'Blog category for functional tests',
            $meta
        );

        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $entityManager->persist($category);
        $entityManager->flush($category);
    }
}
