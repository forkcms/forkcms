<?php

namespace Backend\Modules\Faq\Domain\Feedback;

use Doctrine\ORM\EntityRepository;

final class FeedbackRepository extends EntityRepository
{
    public function findAllForWidget(int $limit): array
    {
        return $this
            ->createQueryBuilder('f')
            ->andWhere('f.processed = :processed')
            ->setParameter(':processed', false)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
