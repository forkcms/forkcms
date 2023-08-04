<?php

namespace ForkCMS\Core\Domain\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

final class CreateSchema
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * Adds new doctrine entities in the database.
     */
    public function forEntityClasses(string ...$entityClasses): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metaData = array_map(
            [$this->entityManager, 'getClassMetadata'],
            $entityClasses
        );
        $schemaTool->updateSchema($metaData, true);
    }
}
