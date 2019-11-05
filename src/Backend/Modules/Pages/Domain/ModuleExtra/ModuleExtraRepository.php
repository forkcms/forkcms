<?php

namespace Backend\Modules\Pages\Domain\ModuleExtra;

use Common\ModuleExtraType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NoResultException;
use RuntimeException;

/**
 * @method ModuleExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleExtra[]    findAll()
 * @method ModuleExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleExtra::class);
    }

    public function add(ModuleExtra $moduleExtra): void
    {
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

    /**
     * @return ModuleExtra[]
     */
    public function getWidgets(): array
    {
        return $this
            ->createQueryBuilder('me')
            ->where('me.type = :type')
            ->andWhere('me.hidden = :hidden')
            ->setParameters(
                [
                    'type' => ModuleExtraType::widget(),
                    'hidden' => false,
                ]
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ModuleExtra[]
     */
    public function getBlocks(): array
    {
        return $this
            ->createQueryBuilder('me', 'me.id')
            ->where('me.type = :type')
            ->andWhere('me.hidden = :hidden')
            ->setParameters(
                [
                    'type' => ModuleExtraType::block(),
                    'hidden' => false,
                ]
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ModuleExtra[]
     */
    public function findModuleExtra(string $module, string $action, ModuleExtraType $moduleExtraType): array
    {
        return $this
            ->createQueryBuilder('me', 'me.id')
            ->where('me.module = :module')
            ->andWhere('me.type = :type')
            ->andWhere('me.action = :action')
            ->setParameters(
                [
                    'module' => $module,
                    'type' => $moduleExtraType,
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
                    'type' => ModuleExtraType::widget(),
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

    public function getModuleExtraId(
        string $module,
        string $action,
        ModuleExtraType $moduleExtraType,
        bool $isDataNull = null,
        bool $isHidden = null
    ): ?int {
        $qb = $this
            ->createQueryBuilder('em')
            ->select('em.id')
            ->where('em.module = :module')
            ->andWhere('em.type = :type')
            ->andWhere('em.action = :action')
            ->setParameters([
                'module' => $module,
                'action' => $action,
                'type' => $moduleExtraType,
            ]);

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

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function updateModuleExtraDataByModuleAndSequence(
        string $module,
        int $sequence,
        $data,
        ModuleExtraType $moduleExtraType
    ): void {
        $moduleExtra = $this->findOneBy(
            [
                'module' => $module,
                'type' => $moduleExtraType,
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
            ->select('MAX(me.sequence) + 1')
            ->where('me.module = :module')
            ->setParameter('module', $module);

        $currentSequenceNumber = (int) $qb
            ->getQuery()
            ->getSingleScalarResult();

        if ($currentSequenceNumber > 0) {
            return $currentSequenceNumber;
        }

        $qb->select('CEIL(MAX(me.sequence) / 1000) * 1000');

        return (int) $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NoResultException
     */
    public function findIdForModuleAndAction(string $module, string $action): int
    {
        $id = $this
            ->createQueryBuilder('me')
            ->select('me.id')
            ->where('me.module = :module AND me.action = :action')
            ->setParameter('module', $module)
            ->setParameter('action', $action)
            ->getQuery()
            ->getSingleScalarResult();

        if ($id === null) {
            throw new NoResultException;
        }

        return (int) $id;
    }
}
