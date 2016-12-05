<?php

namespace Common\Doctrine\Entity;

use Doctrine\DBAL\Exception\TableExistsException;
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
    public function forEntityClass($entityClass)
    {
        $this->forEntityClasses([$entityClass]);
    }

    /**
     * Adds new doctrine entities in the database
     *
     * @param array $entityClasses
     *
     * @throws ToolsException
     */
    public function forEntityClasses(array $entityClasses)
    {
        // create the database table for the given class using the doctrine SchemaTool
        $schemaTool = new SchemaTool($this->entityManager);

        try {
            $schemaTool->createSchema(
                array_map(
                    [$this->entityManager, 'getClassMetadata'],
                    $entityClasses
                )
            );
        } catch (TableExistsException $tableExists) {
            // do nothing
        } catch (ToolsException $toolsException) {
            if (!$toolsException->getPrevious() instanceof TableExistsException) {
                throw $toolsException;
            }
            // do nothing
        }
    }
}
