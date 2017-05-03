<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class TypeDBALType extends StringType
{
    const NAME = 'media_group_type';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Type
    {
        return Type::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }
}
