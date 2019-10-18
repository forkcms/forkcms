<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PageBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageBlock[]    findAll()
 * @method PageBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageBlock::class);
    }

    public function add(PageBlock $pageBlock): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($pageBlock);
    }

    public function save(PageBlock $pageBlock): void
    {
        $this->getEntityManager()->flush($pageBlock);
    }

    public function deleteByRevisionIds(array $ids): void
    {
        $this
            ->createQueryBuilder('em')
            ->delete()
            ->where('em.revisionId IN :ids')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
    }

    public function deleteByExtraId(int $extraId): void
    {
        $this
            ->createQueryBuilder('em')
            ->delete()
            ->where('em.extraId = :extraId')
            ->setParameter('extraId', $extraId)
            ->getQuery()
            ->execute();
    }

    public function clearExtraId(int $extraId): void
    {
        $this
            ->createQueryBuilder('em')
            ->where('em.extraId = :extraId')
            ->set('em.extraId', null)
            ->getQuery()
            ->execute();
    }
}
