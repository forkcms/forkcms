<?php

namespace Backend\Modules\Pages\Domain\ModuleExtra;

use Common\ModuleExtraType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

class ModuleExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleExtra::class);
    }

    public function add(ModuleExtra $moduleExtra): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($moduleExtra);
    }

    public function getWidgets(): array
    {
        return $this
            ->createQueryBuilder('me')
            ->where('me.type = :type')
            ->andWhere('me.hidden = :hidden')
            ->setParameters(
                [
                    'type' => (string) ModuleExtraType::widget(),
                    'hidden' => false,
                ]
            )
            ->getQuery()
            ->getResult();
    }
}
