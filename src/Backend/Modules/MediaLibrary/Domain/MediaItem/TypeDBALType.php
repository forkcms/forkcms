<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class TypeDBALType extends StringType
{
    const NAME = 'media_item_type';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Type
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): Type
    {
        return Type::fromString($value);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }
}
