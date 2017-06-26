<?php

namespace Common\Doctrine\Type;

use Common\Doctrine\ValueObject\AbstractImage;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractImageType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'VARCHAR(255)';
    }

    /**
     * @param string $imageFileName
     * @param AbstractPlatform $platform
     *
     * @return AbstractImage
     */
    public function convertToPHPValue($imageFileName, AbstractPlatform $platform): AbstractImage
    {
        return $this->createFromString($imageFileName);
    }

    /**
     * @param AbstractImage|null $image
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($image, AbstractPlatform $platform): ?string
    {
        return $image !== null ? (string) $image : null;
    }

    abstract protected function createFromString(string $imageFileName): AbstractImage;
}
