<?php

namespace Backend\Modules\Pages\Domain\Page;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function add(Page $page): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($page);
    }

    public function save(Page $page): void
    {
        $this->getEntityManager()->flush($page);
    }

    public function deleteByRevisionIds(array $ids): void
    {
        $this
            ->createQueryBuilder('p')
            ->delete()
            ->where('p.revisionId IN :ids')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
    }
}
