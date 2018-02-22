<?php

namespace App\Backend\Modules\Faq\Domain\Feedback;

use Doctrine\ORM\EntityRepository;

final class FeedbackRepository extends EntityRepository
{
    public function add(Feedback $feedback): void
    {
        $this->getEntityManager()->persist($feedback);
        $this->getEntityManager()->flush();
    }

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
