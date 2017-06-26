<?php

namespace Common\Doctrine\Type;

use Common\Doctrine\ValueObject\AbstractFile;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractFileType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'VARCHAR(255)';
    }

    /**
     * @param string $fileName
     * @param AbstractPlatform $platform
     *
     * @return AbstractFile
     */
    public function convertToPHPValue($fileName, AbstractPlatform $platform): AbstractFile
    {
        return $this->createFromString($fileName);
    }

    /**
     * @param AbstractFile|null $file
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($file, AbstractPlatform $platform): ?string
    {
        return $file !== null ? (string) $file : null;
    }

    abstract protected function createFromString(string $fileName): AbstractFile;
}
