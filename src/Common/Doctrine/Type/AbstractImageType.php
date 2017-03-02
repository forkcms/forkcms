<?php

namespace Common\Doctrine\Type;

use Common\Doctrine\ValueObject\AbstractImage;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractImageType extends Type
{
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
     * @param string $imageFileName
     * @param AbstractPlatform $platform
     *
     * @return AbstractImage
     */
    public function convertToPHPValue($imageFileName, AbstractPlatform $platform)
    {
        return $this->createFromString($imageFileName);
    }

    /**
     * @param AbstractImage $image
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($image, AbstractPlatform $platform)
    {
        return (string) $image;
    }

    /**
     * @param string $imageFileName
     *
     * @return AbstractImage
     */
    abstract protected function createFromString(string $imageFileName): AbstractImage;
}
