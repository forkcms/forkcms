<?php

namespace App\Service\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

class EntitySchema
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Adds a new doctrine entity in the database
     *
     * @param string $entityClass
     */
    public function createForEntityClass(string $entityClass): void
    {
        $this->createForEntityClasses([$entityClass]);
    }

    /**
     * Adds new doctrine entities in the database
     *
     * @param string[] $entityClasses
     */
    public function createForEntityClasses(array $entityClasses): void
    {
        $schemaTool = new SchemaTool($this->entityManager);

        $schemaTool->updateSchema(
            array_map(
                [$this->entityManager, 'getClassMetadata'],
                $entityClasses
            ),
            true
        );
    }
}
