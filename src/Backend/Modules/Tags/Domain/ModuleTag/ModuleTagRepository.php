<?php

namespace Backend\Modules\Tags\Domain\ModuleTag;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

final class ModuleTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleTag::class);
    }

    public function add(ModuleTag ...$moduleTags): void
    {
        foreach ($moduleTags as $moduleTag) {
            $this->getEntityManager()->persist($moduleTag);
        }

        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(ModuleTag ...$moduleTags): void
    {
        foreach ($moduleTags as $moduleTag) {
            $this->getEntityManager()->remove($moduleTag);
            $moduleTag->getTag()->decreaseNumberOfTimesLinked();
        }

        $this->getEntityManager()->flush();
    }

    public function findModulesByTagId(int $id): array
    {
        return array_column(
            $this->createQueryBuilder('mt')
                ->select('mt.moduleName')
                ->innerJoin('mt.tag', 't', Join::WITH, 't.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getScalarResult(),
            'moduleName'
        );
    }
}
