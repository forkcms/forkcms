<?php

namespace Backend\Modules\Faq\Domain\Feedback;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Feedback|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feedback|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feedback[] findAll()
 * @method Feedback[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

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
