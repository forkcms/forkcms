<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * @method Block|null find($id, $lockMode = null, $lockVersion = null)
 * @method Block|null findOneBy(array $criteria, array $orderBy = null)
 * @method Block[] findAll()
 * @method Block[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockRepository extends SortableRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Block::class));
    }

    public function save(Block $block): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($block);
        $entityManager->flush();
    }

    public function remove(Block $block): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($block);
        $entityManager->flush();
    }

    /**
     * @return Block[]
     */
    public function getWidgets(): array
    {
        return $this
            ->createQueryBuilder('b')
            ->where('b.type = :type')
            ->andWhere('b.hidden = :hidden')
            ->setParameters(
                [
                    'type' => Type::WIDGET,
                    'hidden' => false,
                ]
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Block[]
     */
    public function getActions(): array
    {
        return $this
            ->createQueryBuilder('b')
            ->where('b.type = :type')
            ->andWhere('b.hidden = :hidden')
            ->setParameters(
                [
                    'type' => Type::ACTION,
                    'hidden' => false,
                ]
            )
            ->getQuery()
            ->getResult();
    }

    public function findUnique(
        ModuleBlock $moduleBlock,
        SettingsBag $settings = new SettingsBag()
    ): ?Block {
        $queryBuilder = $this->createQueryBuilder('b')
            ->andWhere('b.block.module = :module')
            ->setParameter('module', $moduleBlock->getModule()->getName())
            ->andWhere('b.block.name = :name')
            ->setParameter('name', BlockNameDBALType::prefixedString($moduleBlock->getName()))
            ->andWhere('b.type = :type')
            ->setParameter('type', $moduleBlock->getName()->getType()->value)
            ->andWhere('JSON_CONTAINS(b.settings, :settings) = 1')
            ->setParameter('settings', $settings->asJsonString());

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /** @return Block[] */
    public function findAllWidgets(): array
    {
        /* @TODO add check for widgets that have been added but aren't in the database yet */
        return $this->findBy(
            ['type' => Type::WIDGET->value, 'hidden' => false],
            ['type' => Criteria::ASC, 'position' => Criteria::ASC]
        );
    }

    /** @return Block[] */
    public function findAllActions(): array
    {
        /* @TODO add check for actions that have been added but aren't in the database yet */
        return $this->findBy(
            ['type' => Type::ACTION->value, 'hidden' => false],
            ['type' => Criteria::ASC, 'position' => Criteria::ASC]
        );
    }
}
