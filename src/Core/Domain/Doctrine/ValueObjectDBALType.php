<?php

namespace ForkCMS\Core\Domain\Doctrine;

use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Stringable;

abstract class ValueObjectDBALType extends StringType
{
    use ForkDBALTypeName;

    final public function convertToPHPValue($value, AbstractPlatform $platform): null|Stringable|BackedEnum
    {
        if ($value === null) {
            return null;
        }

        return $this->fromString($value);
    }

    final public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $this->toString($value);
    }

    abstract protected function fromString(string $value): null|Stringable|BackedEnum;

    protected function toString(Stringable|BackedEnum $value): string
    {
        if ($value instanceof Stringable) {
            return (string) $value;
        }

        return $value->value;
    }
}
