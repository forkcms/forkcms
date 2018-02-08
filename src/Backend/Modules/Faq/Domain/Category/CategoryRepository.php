<?php

namespace Backend\Modules\Faq\Domain\Category;

use App\Component\Model\Model;
use Doctrine\ORM\EntityRepository;
use App\Component\Locale\Locale;
use Doctrine\ORM\NoResultException;

final class CategoryRepository extends EntityRepository
{
    public function add(Category $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }

    public function remove(Category $category): void
    {
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $url
     * @param Locale $locale
     *
     * @return Category
     *
     * @throws NoResultException When no result is found
     */
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

    public function getUrl(string $url, Locale $locale, int $id = null): string
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
