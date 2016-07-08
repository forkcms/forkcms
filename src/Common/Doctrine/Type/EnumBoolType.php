<?php

namespace Common\Doctrine\Type;

use Backend\Modules\ContentBlocks\ValueObject\Status;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EnumBoolType extends Type
{
    const ENUM_BOOL = 'enum_bool';

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'ENUM("Y","N")';
    }

    /**
     * @param string $enumBool
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function convertToPHPValue($enumBool, AbstractPlatform $platform)
    {
        return $enumBool === 'Y';
    }

    /**
     * @param bool $bool
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($bool, AbstractPlatform $platform)
    {
        return ($bool) ? 'Y' : 'N';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::ENUM_BOOL;
    }
}
