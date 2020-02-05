<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\FloatType;

final class AspectRatioDBALType extends FloatType
{
    public function getName(): string
    {
        return 'media_item_aspect_ratio';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $fieldDeclaration['precision'] = 13;
        $fieldDeclaration['scale'] = 2;

        return parent::getSQLDeclaration($fieldDeclaration, $platform);
    }

    public function convertToPHPValue($aspectRatio, AbstractPlatform $platform): ?AspectRatio
    {
        if ($aspectRatio === null) {
            return null;
        }

        return new AspectRatio($aspectRatio);
    }

    public function convertToDatabaseValue($aspectRatio, AbstractPlatform $platform): ?float
    {
        if ($aspectRatio === null) {
            return null;
        }

        return $aspectRatio->asFloat();
    }
}
