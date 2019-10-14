<?php

namespace Backend\Modules\Pages\Domain\ModuleExtra;

use Common\ModuleExtraType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use RuntimeException;

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

    public function delete(ModuleExtra $moduleExtra): void
    {
        $this->getEntityManager()->remove($moduleExtra);
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

    public function getWidgetId(string $module, string $action, bool $isDataNull = null, bool $isHidden = null): ?int
    {
        /** @var ModuleExtra|null $moduleExtra */
        $qb = $this
            ->createQueryBuilder('em')
            ->where('em.module = :module')
            ->andWhere('em.type = :type')
            ->andWhere('em.action = :action')
            ->setParameter('module', $module)
            ->setParameter('type', (string) ModuleExtraType::widget())
            ->setParameter('action', $action);

        if ($isDataNull === true) {
            $qb
                ->andWhere($qb->expr()->isNull('em.data'));
        }

        if ($isDataNull === false) {
            $qb
                ->andWhere($qb->expr()->isNotNull('em.data'));
        }

        if ($isHidden !== null) {
            $qb
                ->andWhere('em.hidden = :isHidden')
                ->setParameter('isHidden', $isHidden);
        }

        $moduleExtra = $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$moduleExtra instanceof ModuleExtra) {
            return null;
        }

        return $moduleExtra->getId();
    }

    public function updateWidgetDataByModuleAndSequence(string $module, string $sequence, $data): void
    {
        $moduleExtra = $this->findOneBy(
            [
                'module' => $module,
                'type' => 'widget',
                'sequence' => $sequence,
            ]
        );

        if (!$moduleExtra instanceof ModuleExtra) {
            throw new RuntimeException('Widget not found');
        }

        $moduleExtra->update(
            $moduleExtra->getModule(),
            $moduleExtra->getType(),
            $moduleExtra->getLabel(),
            $moduleExtra->getAction(),
            $data,
            $moduleExtra->isHidden(),
            $moduleExtra->getSequence()
        );

        $this->save($moduleExtra);
    }

    public function getNextSequenceByModule(string $module): int
    {
        $qb = $this->createQueryBuilder('me');
        $qb
            ->select($qb->expr()->max('me.sequence'))
            ->where('me.module = :module')
            ->setParameter('module', $module);

        $currentSequenceNumber = (int) $qb
            ->getQuery()
            ->getSingleScalarResult();

        return $currentSequenceNumber + 1;
    }
}
