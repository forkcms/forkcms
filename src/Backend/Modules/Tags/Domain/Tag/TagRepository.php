<?php

namespace Backend\Modules\Tags\Domain\Tag;

use Backend\Core\Engine\Model;
use Common\Locale;
use Common\Uri;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

final class TagRepository extends EntityRepository
{
    public function add(Tag $tag): void
    {
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Tag ...$tag): void
    {
        $entityManager = $this->getEntityManager();

        array_map(
            function (Tag $tag) use ($entityManager) {
                $entityManager->remove($tag);
            },
            $tag
        );

        $this->getEntityManager()->flush();
    }

    public function findByIds(int ...$id): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->where($queryBuilder->expr()->in('t.id', $id))
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
}
