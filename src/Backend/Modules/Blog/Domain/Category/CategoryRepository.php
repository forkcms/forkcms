<?php

namespace Backend\Modules\Blog\Domain\Category;

use Common\Core\Model;
use Common\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[] findAll()
 * @method Category[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

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
