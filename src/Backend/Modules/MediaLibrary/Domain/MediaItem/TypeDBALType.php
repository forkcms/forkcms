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
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'VARCHAR(255)';
    }

    /**
     * @param string $status
     * @param AbstractPlatform $platform
     *
     * @return Type
     */
    public function convertToPHPValue($status, AbstractPlatform $platform)
    {
        return Type::fromString($status);
    }

    /**
     * @param Type $status
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($status, AbstractPlatform $platform)
    {
        return (string) $status;
    }
}
