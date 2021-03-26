<?php

namespace Backend\Modules\Faq\Domain\Question;

use Backend\Modules\Faq\Domain\Category\Category;
use Common\Core\Model;
use Common\Locale;
use Doctrine\ORM\NoResultException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[] findAll()
 * @method Question[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function add(Question $question): void
    {
        $this->getEntityManager()->persist($question);
        $this->getEntityManager()->flush();
    }

    public function remove(Question $question): void
    {
        $this->getEntityManager()->remove($question);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $url
     * @param Locale $locale
     *
     * @return Question
     *
     * @throws NoResultException When no result is found
     */
    public function findOneByUrl(string $url, Locale $locale): Question
    {
        return $this
            ->createQueryBuilder('q')
            ->innerJoin('q.meta', 'm')
            ->andWhere('m.url = :url')
            ->andWhere('q.locale = :locale')
            ->setParameter(':url', $url)
            ->setParameter(':locale', $locale)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function findByCategory(
        Category $category,
        Locale $locale,
        int $limit = null,
        array $excludeIds = array()
    ): array {
        $query = $this
            ->createQueryBuilder('q')
            ->andWhere('q.category = :category')
            ->andWhere('q.hidden = :hidden')
            ->andWhere('q.locale = :locale')
            ->setParameter(':category', $category)
            ->setParameter(':hidden', false)
            ->setParameter(':locale', $locale)
            ->orderBy('q.sequence', 'DESC')
        ;

        if ($limit !== null) {
            $query->setMaxResults($limit);
        }

        if (!empty($excludeIds)) {
            $query->andWhere(
                $query->expr()->not(
                    $query->expr()->in('q.id', $excludeIds)
                )
            );
        }

        return $query->getQuery()->getResult();
    }

    public function findMultiple(array $ids, Locale $locale): array
    {
        return $this->findBy(
            [
                'id' => $ids,
                'locale' => $locale->getLocale(),
                'hidden' => false,
            ],
            ['question' => 'ASC']
        );
    }

    public function findMostRead(int $limit, Locale $locale): array
    {
        return $this
            ->createQueryBuilder('q')
            ->andWhere('q.numberOfViews > 0')
            ->andWhere('q.locale = :locale')
            ->andWhere('q.hidden = :hidden')
            ->setParameter(':locale', $locale)
            ->setParameter(':hidden', false)
            ->orderBy('q.numberOfUsefulYes + q.numberOfUsefulNo', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMaximumSequence(Category $category, Locale $locale): int
    {
        $queryBuilder = $this->createQueryBuilder('q');

        return (int) $queryBuilder
            ->select($queryBuilder->expr()->max('q.sequence'))
            ->andWhere('q.category = :category')
            ->andWhere('q.locale = :locale')
            ->setParameter(':category', $category)
            ->setParameter(':locale', $locale)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getUrl(string $url, Locale $locale, int $id = null): string
    {
        $queryBuilder = $this->createQueryBuilder('q');

        $query = $queryBuilder
            ->select($queryBuilder->expr()->count('q.id'))
            ->innerJoin('q.meta', 'm')
            ->andWhere('m.url = :url')
            ->andWhere('q.locale = :locale')
            ->setParameter(':url', $url)
            ->setParameter(':locale', $locale)
        ;

        if ($id !== null) {
            $query
                ->andWhere('q.id != :id')
                ->setParameter(':id', $id)
            ;
        }

        if ((int) $query->getQuery()->getSingleScalarResult() === 0) {
            return $url;
        }

        return $this->getUrl(Model::addNumber($url), $locale, $id);
    }
}
