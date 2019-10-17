<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
}
