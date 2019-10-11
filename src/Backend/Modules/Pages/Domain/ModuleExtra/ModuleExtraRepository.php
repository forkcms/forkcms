<?php

namespace Backend\Modules\Pages\Domain\ModuleExtra;

use Common\ModuleExtraType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    public function save(ModuleExtra $moduleExtra): void
    {
        $this->getEntityManager()->flush($moduleExtra);
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

    public function getBlocks(): array
    {
        return $this
            ->createQueryBuilder('me', 'me.id')
            ->where('me.type = :type')
            ->andWhere('me.hidden = :hidden')
            ->setParameters(
                [
                    'type' => (string) ModuleExtraType::block(),
                    'hidden' => false,
                ]
            )
            ->getQuery()
            ->getResult();
    }

    public function findWidgetsByModuleAndAction(string $module, string $action): array
    {
        return $this
            ->createQueryBuilder('me', 'me.id')
            ->where('me.module = :module')
            ->andWhere('me.type = :type')
            ->andWhere('me.action = :action')
            ->setParameters(
                [
                    'module' => $module,
                    'type' => (string) ModuleExtraType::widget(),
                    'action' => $action,
                ]
            )
            ->getQuery()
            ->getResult();
    }

    public function getWidgetDataByModuleAndActionAndItemId(string $module, string $action, ?int $id): ?string
    {
        $results = $this
            ->createQueryBuilder('me')
            ->select('me.data')
            ->where('me.module = :module')
            ->andWhere('me.type = :type')
            ->andWhere('me.action = :action')
            ->andWhere('me.data LIKE :id')
            ->setParameters(
                [
                    'module' => $module,
                    'type' => (string) ModuleExtraType::widget(),
                    'action' => $action,
                    'id' => '%s:2:"id";i:' . $id . ';%',
                ]
            )
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        if (count($results) === 0 || !array_key_exists('data', $results[0])) {
            return null;
        }

        return $results[0]['data'];
    }

    public function getWidgetId(string $module, string $action): ?int
    {
        /** @var ModuleExtra|null $moduleExtra */
        $moduleExtra = $this
            ->createQueryBuilder('em')
            ->where('em.module = :module')
            ->andWhere('em.type = :type')
            ->andWhere('em.action = :action')
            ->setParameters(
                [
                    'module' => $module,
                    'type' => (string) ModuleExtraType::widget(),
                    'action' => $action,
                ]
            )
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$moduleExtra instanceof ModuleExtra) {
            return null;
        }

        return $moduleExtra->getId();
    }
}
