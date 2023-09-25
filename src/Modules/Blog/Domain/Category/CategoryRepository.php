<?php

namespace ForkCMS\Modules\Blog\Domain\Category;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaCallbackService;
use ForkCMS\Modules\Frontend\Domain\Meta\RepositoryWithMetaTrait;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;

class CategoryRepository extends ServiceEntityRepository implements MetaCallbackService
{
    /** @phpstan-use RepositoryWithMetaTrait<Revision> */
    use RepositoryWithMetaTrait;

    public function __construct(ManagerRegistry $managerRegistry) {
        parent::__construct($managerRegistry, Category::class);
    }

    public function save(Category $category): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($category);
        $entityManager->flush();
    }

    public function remove(Category $category): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($category);
        $entityManager->flush();
    }

    public function generateSlug(): string
    {
        return 'TODO';
        // return $this->slugify();
    }

    protected function slugifyIdQueryBuilder(QueryBuilder $queryBuilder, ?object $subject, Locale $locale, string $entityAlias): void
    {
        // TODO: Implement slugifyIdQueryBuilder() method.
    }
}
