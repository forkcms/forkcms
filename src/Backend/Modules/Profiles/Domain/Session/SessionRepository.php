<?php

namespace Backend\Modules\Profiles\Domain\Session;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[] findAll()
 * @method Session[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function add(Session $session): void
    {
        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();
    }

    public function remove(Session $session): void
    {
        $this->getEntityManager()->remove($session);
        $this->getEntityManager()->flush();
    }

    /**
     * Remove all sessions with date older then 1 month
     */
    public function cleanup(): void
    {
        $sessions = $this
            ->createQueryBuilder('s')
            ->where('s.date <= :date')
            ->setParameter(':date', new DateTimeImmutable('-1 month'))
            ->getQuery()
            ->getResult();

        foreach ($sessions as $session) {
            $this->remove($session);
        }
    }
}
