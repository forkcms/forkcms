<?php

namespace ForkCMS\Modules\Blog\Domain\Category;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Blog\Domain\Article\Status;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaCallbackService;
use ForkCMS\Modules\Frontend\Domain\Meta\RepositoryWithMetaTrait;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use DateTime;

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
        $category->getMeta()->setSlug($this->slugify($category->getTitle(), $category, $category->getLocale()));
        $entityManager->persist($category);
        $entityManager->flush();
    }

    public function remove(Category $category): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($category);
        $entityManager->flush();
    }

    public function generateSlug(string $slug, Locale $locale, ?int $id): string
    {
        if ($id === null) {
            return $this->slugify($slug, null, $locale);
        }

        return $this->slugify($slug, $this->findOneBy(['id' => $id, 'locale' => $locale->value]), $locale);
    }

    protected function slugifyIdQueryBuilder(QueryBuilder $queryBuilder, ?object $subject, Locale $locale, string $entityAlias): void
    {
        // TODO: Implement slugifyIdQueryBuilder() method.
    }

    public function getCategoryBySlug(string $slug, Locale $locale): ?Category
    {
        return $this->createQueryBuilder('c')
            ->select('c, m')
            ->innerJoin('c.meta', 'm')
            ->andWhere('m.slug = :slug')
            ->andWhere('c.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAllCategories(string $locale): array
    {
        return $this->createQueryBuilder('c')
            ->select('c, m, p, pm')
            ->innerJoin('c.posts', 'p')
            ->innerJoin('c.meta', 'm')
            ->innerJoin('p.meta', 'pm')
            ->andWhere('c.locale = :locale')
            ->andWhere('p.status = :status')
            ->andWhere('p.hidden = :false')
            ->andWhere('p.publishOn <= :now')
            ->setParameter('locale', $locale)
            ->setParameter('status', Status::ACTIVE)
            ->setParameter('false', false)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();
    }
}
