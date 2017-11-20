<?php

namespace Backend\Modules\Faq\Domain\Category;

use Common\Core\Model;
use Doctrine\ORM\EntityRepository;
use Common\Locale;

final class CategoryRepository extends EntityRepository
{
    public function findOneByUrl(string $url, Locale $locale): Category
    {
        return $this
            ->createQueryBuilder('c')
            ->innerJoin('c.meta', 'm')
            ->andWhere('m.url = :url')
            ->andWhere('c.locale = :locale')
            ->setParameter(':url', $url)
            ->setParameter(':locale', $locale)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function findMaximumSequence(Locale $locale): int
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return (int) $queryBuilder
            ->select($queryBuilder->expr()->max('c.sequence'))
            ->andWhere('c.locale = :locale')
            ->setParameter(':locale', $locale)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findCount(Locale $locale)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return (int) $queryBuilder
            ->select($queryBuilder->expr()->count('c.id'))
            ->andWhere('c.locale = :locale')
            ->setParameter(':locale', $locale)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getUrl(string $url, Locale $locale, ?int $id = null): string
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $query = $queryBuilder
            ->select($queryBuilder->expr()->count('c.id'))
            ->innerJoin('c.meta', 'm')
            ->andWhere('m.url = :url')
            ->andWhere('c.locale = :locale')
            ->setParameter(':url', $url)
            ->setParameter(':locale', $locale)
        ;

        if ($id !== null) {
            $query
                ->andWhere('c.id != :id')
                ->setParameter(':id', $id)
            ;
        }

        if ((int) $query->getQuery()->getSingleScalarResult() === 0) {
            return $url;
        }

        return $this->getUrl(Model::addNumber($url), $locale, $id);
    }
}
