<?php

namespace ForkCMS\Modules\Blog\Domain\Article;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaCallbackService;
use ForkCMS\Modules\Frontend\Domain\Meta\RepositoryWithMetaTrait;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;

class ArticleRepository extends ServiceEntityRepository implements MetaCallbackService
{
    /** @phpstan-use RepositoryWithMetaTrait<Revision> */
    use RepositoryWithMetaTrait;

    public function __construct(ManagerRegistry $managerRegistry) {
        parent::__construct($managerRegistry, Article::class);
    }

    public function save(Article $article): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($article);
        $entityManager->flush();
    }

    public function remove(Article $article): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($article);
        $entityManager->flush();
    }

    public function getNextIdForLanguage(Locale $locale): int
    {
        return (int) $this->getEntityManager()
                ->createQueryBuilder()
                ->select('MAX(a.id) as id')
                ->from(Article::class, 'a')
                ->where('a.locale = :locale')
                ->setParameter('locale', $locale)
                ->getQuery()
                ->getSingleScalarResult() + 1;
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
