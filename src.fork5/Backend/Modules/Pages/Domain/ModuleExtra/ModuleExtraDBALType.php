<?php

namespace Backend\Modules\Pages\Domain\ModuleExtra;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class ModuleExtraDBALType extends StringType
{
    const NAME = 'module_extra_type';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ModuleExtraType
    {
        if ($value === null) {
            return null;
        }

        return new ModuleExtraType($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }
}
