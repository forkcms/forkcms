<?php

namespace ForkCMS\Core\Domain\Doctrine;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Container;

final class ForkNamingStrategy extends UnderscoreNamingStrategy
{
    public function __construct()
    {
        parent::__construct(CASE_LOWER, true);
    }

    public function propertyToColumnName($propertyName, $className = null): string
    {
        return $propertyName;
    }

    public function classToTableName($className): string
    {
        $underscoredClassName = parent::classToTableName($className);

        try {
            $moduleName = ModuleName::fromFQCN($className)->getName();
        } catch (InvalidArgumentException) {
            return $underscoredClassName;
        }

        return Container::underscore($moduleName) . '__' . $underscoredClassName;
    }

    public function joinColumnName($propertyName, $className = null): string
    {
        return $this->propertyToColumnName($propertyName);
    }

    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null): string
    {
        $sourceModuleName = ModuleName::fromFQCN($sourceEntity);
        $targetModuleName = ModuleName::fromFQCN($targetEntity);

        if ($sourceModuleName === $targetModuleName) {
            return $this->classToTableName($sourceEntity) . '__has__' . parent::classToTableName($targetEntity);
        }
        return $this->classToTableName($sourceEntity) . '__has__' . $this->classToTableName($targetEntity);
    }

    public function joinKeyColumnName($entityName, $referencedColumnName = null): string
    {
        if ($referencedColumnName === $this->referenceColumnName()) {
            $referencedColumnName = null;
        }

        return $this->classToTableName($entityName) . ucfirst($referencedColumnName ?: '');
    }
}
