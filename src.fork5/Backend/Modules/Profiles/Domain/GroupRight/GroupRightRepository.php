<?php

namespace Backend\Modules\Profiles\Domain\GroupRight;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupRight|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupRight|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupRight[] findAll()
 * @method GroupRight[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class GroupRightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupRight::class);
    }

    public function add(GroupRight $groupRight): void
    {
        $this->getEntityManager()->persist($groupRight);
        $this->getEntityManager()->flush();
    }

    public function remove(GroupRight $groupRight): void
    {
        $this->getEntityManager()->remove($groupRight);
        $this->getEntityManager()->flush();
    }

    public function findLinkedToProfile(?Profile $profile, int $includeId = null) : array
    {
        if ($profile === null) {
            return $this->findAll();
        }

        $query = $this->createQueryBuilder('r')
            ->where('r.profile = :profile')
            ->setParameter(':profile', $profile);

        if ($includeId !== null) {
            $query
                ->andWhere('r.id != :id')
                ->setParameter(':id', $includeId);
        }

        return $query->getQuery()->getResult();
    }
}
