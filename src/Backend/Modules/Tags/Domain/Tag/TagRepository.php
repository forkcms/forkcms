<?php

namespace Backend\Modules\Tags\Domain\Tag;

use Backend\Core\Engine\Model;
use Common\Locale;
use Common\Uri;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

final class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function add(Tag $tag): void
    {
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Tag ...$tags): void
    {
        $entityManager = $this->getEntityManager();

        foreach ($tags as $tag) {
            $entityManager->remove($tag);
        }

        $this->getEntityManager()->flush();
    }

    public function findByIds(int ...$ids): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->where($queryBuilder->expr()->in('t.id', $ids))
            ->getQuery()
            ->getResult();
    }

    public function findByTagStartingWith(string $term, Locale $locale): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->orderBy('t.tag', Criteria::ASC)
            ->where('t.tag LIKE :term AND t.locale = :locale')
            ->setParameter('term', $term . '%')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

    public function getUrl(string $url, Locale $locale, int $id = null): string
    {
        $url = Uri::getUrl($url);
        $queryBuilder = $this->createQueryBuilder('t');

        $queryBuilder
            ->select('1')
            ->where('t.url = :url AND t.locale = :locale')
            ->setParameter('url', $url)
            ->setParameter('locale', $locale);

        if ($id !== null) {
            $queryBuilder
                ->andWhere('t.id != :id')
                ->setParameter('id', $id);
        }

        if (empty($queryBuilder->getQuery()->getScalarResult())) {
            return $url;
        }

        return $this->getUrl(Model::addNumber($url), $locale, $id);
    }

    public function findTags(Locale $locale, string $moduleName, int $moduleId): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->orderBy('t.tag', Criteria::ASC)
            ->innerJoin(
                't.moduleTags',
                'mt',
                Join::WITH,
                'mt.moduleName = :moduleName AND mt.moduleId = :moduleId AND t.locale = :locale'
            )
            ->indexBy('t', 't.tag')
            ->setParameter('moduleName', $moduleName)
            ->setParameter('moduleId', $moduleId)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

    public function removeUnused(): void
    {
        $this->remove(...$this->findByNumberOfTimesLinked(0));
    }

    public function findAllLinkedTags(Locale $locale): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->where('t.locale = :locale AND t.numberOfTimesLinked > 0')
            ->setParameter('locale', $locale)
            ->orderBy('t.tag')
            ->getQuery()
            ->getResult();
    }

    public function findMostUsed(Locale $locale, int $limit): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->where('t.locale = :locale AND t.numberOfTimesLinked > 0')
            ->setParameter('locale', $locale)
            ->orderBy('t.numberOfTimesLinked', Criteria::DESC)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
