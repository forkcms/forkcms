<?php

namespace Backend\Modules\ContentBlocks\DBALType;

use Backend\Modules\ContentBlocks\ValueObject\ContentBlockStatus;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ContentBlockStatusDBALType extends Type
{
    const CONTENT_BLOCKS_STATUS = 'content_blocks_status';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'ENUM("' . implode('","', ContentBlockStatus::getPossibleStatuses()) . '")';
    }

    public function convertToPHPValue($status, AbstractPlatform $platform): ContentBlockStatus
    {
        return ContentBlockStatus::fromString($status);
    }

    public function convertToDatabaseValue($status, AbstractPlatform $platform): string
    {
        return (string) $status;
    }

    public function getName(): string
    {
        return self::CONTENT_BLOCKS_STATUS;
    }
}
