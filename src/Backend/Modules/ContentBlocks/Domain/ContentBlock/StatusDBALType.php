<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock;

use Backend\Modules\ContentBlocks\Domain\ContentBlock\Status;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class StatusDBALType extends Type
{
    const CONTENT_BLOCKS_STATUS = 'content_blocks_status';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'ENUM("' . implode('","', Status::getPossibleStatuses()) . '")';
    }

    public function convertToPHPValue($status, AbstractPlatform $platform): Status
    {
        return Status::fromString($status);
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
