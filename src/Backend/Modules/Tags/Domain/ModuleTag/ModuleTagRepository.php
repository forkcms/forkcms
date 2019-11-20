<?php

namespace Backend\Modules\Tags\Domain\ModuleTag;

use Common\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
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
                ->groupBy('mt.moduleName')
                ->getQuery()
                ->getScalarResult(),
            'moduleName'
        );
    }

    public function findRelatedModuleIdsByTags($moduleId, $moduleName, $otherModuleName, $limit): array
    {
        return array_column(
            $this->createQueryBuilder('mt')
                ->select('mt2.moduleId')
                ->innerJoin('mt.tag', 't', Join::WITH, 'mt.moduleId = :moduleId AND mt.moduleName = :moduleName')
                ->setParameter('moduleId', $moduleId)
                ->setParameter('moduleName', $moduleName)
                ->innerJoin(
                    't.moduleTags',
                    'mt2',
                    Join::WITH,
                    'mt2.moduleName = :otherModuleName AND (mt2.moduleName != mt.moduleName OR mt2.moduleId != mt.moduleId)'
                )
                ->groupBy('mt2.moduleId')
                ->orderBy('COUNT(mt2.tag)', Criteria::DESC)
                ->setParameter('otherModuleName', $otherModuleName)
                ->setMaxResults($limit)
                ->getQuery()
                ->getScalarResult(),
            'moduleId'
        );
    }

    public function findByTagAndLocale(string $tag, Locale $locale): array
    {
        return $this->createQueryBuilder('mt')
            ->innerJoin('mt.tag', 't', Join::WITH, 't.tag = :tag AND t.locale = :locale')
            ->setParameter('tag', $tag)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

    public function findByModuleAndTag(string $module, int $tagId): array
    {
        return $this->createQueryBuilder('mt')
            ->innerJoin('mt.tag', 't', Join::WITH, 't.id = :tagId AND mt.moduleName = :moduleName')
            ->setParameter('tagId', $tagId)
            ->setParameter('moduleName', $module)
            ->indexBy('mt', 'mt.moduleId')
            ->getQuery()
            ->getResult();
    }
}
