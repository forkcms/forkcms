<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class StatusDBALType extends StringType
{
    public const NAME = 'media_gallery_status';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Status
    {
        return Status::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }
}
