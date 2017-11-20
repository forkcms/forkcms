<?php

namespace Backend\Modules\Faq\Domain\Question;

use Backend\Modules\Faq\Domain\Category\Category;
use Doctrine\ORM\EntityRepository;
use Common\Locale;

final class QuestionRepository extends EntityRepository
{
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
        ?int $limit = null,
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
}
