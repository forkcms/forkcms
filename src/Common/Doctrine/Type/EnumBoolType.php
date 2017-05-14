<?php

namespace Common\Doctrine\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @TODO Remove this in favour of the doctrine bool type, for now it is used because database changes are BB
 *
 * @deprecated
 */
class EnumBoolType extends Type
{
    const ENUM_BOOL = 'enum_bool';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'ENUM("Y","N")';
    }

    /**
     * @param string $enumBool
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function convertToPHPValue($enumBool, AbstractPlatform $platform): bool
    {
        return $enumBool === 'Y';
    }

    /**
     * @param bool $bool
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($bool, AbstractPlatform $platform): string
    {
        return $bool ? 'Y' : 'N';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::ENUM_BOOL;
    }
}
