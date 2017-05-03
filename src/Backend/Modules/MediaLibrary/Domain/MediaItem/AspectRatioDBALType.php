<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DecimalType;

final class AspectRatioDBALType extends DecimalType
{
    public function getName(): string
    {
        return 'media_item_aspect_ratio';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $fieldDeclaration['precision'] = 21;
        $fieldDeclaration['scale'] = 11;

        return parent::getSQLDeclaration($fieldDeclaration, $platform);
    }

    public function convertToPHPValue($aspectRatio, AbstractPlatform $platform): AspectRatio
    {
        return new AspectRatio($aspectRatio);
    }

    public function convertToDatabaseValue($aspectRatio, AbstractPlatform $platform): float
    {
        return $aspectRatio->asFloat();
    }

}
