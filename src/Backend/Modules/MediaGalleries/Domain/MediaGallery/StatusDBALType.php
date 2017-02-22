<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class StatusDBALType extends Type
{
    const NAME = 'media_gallery_status';

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
     * @return Status
     */
    public function convertToPHPValue($status, AbstractPlatform $platform)
    {
        return Status::fromString($status);
    }

    /**
     * @param Status $status
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($status, AbstractPlatform $platform)
    {
        return (string) $status;
    }
}
