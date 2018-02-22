<?php

namespace App\Backend\Modules\Blog\Domain\Category;

use App\Common\Core\Model;
use App\Common\Locale;
use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
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

    public function remove(Category $category): void
    {
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush($category);
    }

    public function getUrl(string $url, int $id = null): string
    {
        $query = $this
            ->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->innerJoin('i.meta', 'm')
            ->where('m.url = :URL')
            ->setParameter('URL', $url);

        if ($id !== null) {
            $query
                ->andWhere('i != :category')
                ->setParameter(
                    'category',
                    $this->getEntityManager()->getReference(
                        Category::class,
                        $id
                    )
                );
        }

        if ((int) $query->getQuery()->getSingleScalarResult() === 0) {
            return $url;
        }

        return $this->getURL(Model::addNumber($url), $id);
    }
}
