<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class PageBlockDBALType extends StringType
{
    const NAME = 'page_block_type';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): PageBlockType
    {
        if ($value === null) {
            return null;
        }
        return new PageBlockType($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }
}
