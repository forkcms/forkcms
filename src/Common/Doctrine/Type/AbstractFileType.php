<?php

namespace Common\Doctrine\Type;

use Common\Doctrine\ValueObject\AbstractFile;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractFileType extends Type
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
     * @param string $fileName
     * @param AbstractPlatform $platform
     *
     * @return AbstractFile
     */
    public function convertToPHPValue($fileName, AbstractPlatform $platform)
    {
        return $this->createFromString($fileName);
    }

    /**
     * @param AbstractFile $file
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($file, AbstractPlatform $platform)
    {
        return (string) $file;
    }

    /**
     * @param string $fileName
     *
     * @return AbstractFile
     */
    abstract protected function createFromString(string $fileName): AbstractFile;
}
