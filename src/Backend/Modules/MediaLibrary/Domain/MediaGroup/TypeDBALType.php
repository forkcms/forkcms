<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineDBALType;

final class TypeDBALType extends DoctrineDBALType
{
    const NAME = 'media_group_type';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'VARCHAR(255)';
    }

    /**
     * @param string $type
     * @param AbstractPlatform $platform
     *
     * @return Type
     */
    public function convertToPHPValue($type, AbstractPlatform $platform)
    {
        return Type::fromString($type);
    }

    /**
     * @param Type $type
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($type, AbstractPlatform $platform)
    {
        return (string) $type;
    }
}
