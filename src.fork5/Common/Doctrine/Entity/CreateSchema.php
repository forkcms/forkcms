<?php

namespace Common\Doctrine\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

class CreateSchema
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
    public function forEntityClass(string $entityClass): void
    {
        $this->forEntityClasses([$entityClass]);
    }

    /**
     * Adds new doctrine entities in the database
     *
     * @param string[] $entityClasses
     *
     * @throws ToolsException
     */
    public function forEntityClasses(array $entityClasses): void
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
