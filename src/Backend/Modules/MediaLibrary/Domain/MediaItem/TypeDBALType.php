<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineDBALType;

final class TypeDBALType extends DoctrineDBALType
{
    const NAME = 'media_item_type';

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
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'VARCHAR(255)';
    }

    /**
     * @param string $value
     * @param AbstractPlatform $platform
     * @return Type
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return Type::fromString($value);
    }

    /**
     * @param Type $value
     * @param AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string) $value;
    }
}
