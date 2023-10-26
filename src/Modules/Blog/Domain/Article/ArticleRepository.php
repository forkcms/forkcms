<?php

namespace ForkCMS\Modules\Blog\Domain\Article;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaCallbackService;
use ForkCMS\Modules\Frontend\Domain\Meta\RepositoryWithMetaTrait;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use DateTime;

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
        $article->getMeta()->setSlug($this->slugify($article->getTitle(), $article, $article->getLocale()));
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

    public function generateSlug(string $slug, Locale $locale, ?int $revisionId): string
    {
        if ($revisionId === null) {
            return $this->slugify($slug, null, $locale);
        }

        return $this->slugify($slug, $this->findOneBy(['revisionId' => $revisionId, 'locale' => $locale->value]), $locale);
    }

    protected function slugifyIdQueryBuilder(QueryBuilder $queryBuilder, ?object $subject, Locale $locale, string $entityAlias): void
    {
        // TODO: Implement slugifyIdQueryBuilder() method.
    }

    private function getBaseQuery(Locale $locale): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->select('a, c, m')
            ->innerJoin('a.category', 'c')
            ->innerJoin('a.meta', 'm')
            ->innerJoin('a.createdBy', 'cb')
            ->andWhere('a.locale = :locale')
            ->andWhere('a.status = :status')
            ->andWhere('a.hidden = :false')
            ->andWhere('a.publishOn <= :now')
            ->setParameter('locale', $locale)
            ->setParameter('status', Status::ACTIVE)
            ->setParameter('false', false)
            ->setParameter('now', new DateTime());
    }

    // TODO make actually paginated
    public function getAllPaginated(string $language): array
    {
        return $this->getBaseQuery(Locale::from($language))
            ->getQuery()
            ->getResult();
    }

    public function getFullBlogPostBySlug(string $slug, Locale $locale): ?Article
    {
        return $this->getBaseQuery($locale)
            ->andWhere('m.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getFullBlogPostByRevisionId(int $revisionId, Locale $locale): ?Article
    {
        return $this->getBaseQuery($locale)
            ->andWhere('a.revisionId = :revisionId')
            ->setParameter('revisionId', $revisionId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
