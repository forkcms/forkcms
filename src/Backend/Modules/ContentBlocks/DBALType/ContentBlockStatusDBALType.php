<?php

namespace Backend\Modules\ContentBlocks\DBALType;

use Backend\Modules\ContentBlocks\ValueObject\ContentBlockStatus;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ContentBlockStatusDBALType extends Type
{
    const CONTENT_BLOCKS_STATUS = 'content_blocks_status';

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'ENUM("' . implode('","', ContentBlockStatus::getPossibleStatuses()) . '")';
    }

    /**
     * @param string $status
     * @param AbstractPlatform $platform
     *
     * @return ContentBlockStatus
     */
    public function convertToPHPValue($status, AbstractPlatform $platform)
    {
        return ContentBlockStatus::fromString($status);
    }

    /**
     * @param ContentBlockStatus $status
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($status, AbstractPlatform $platform)
    {
        return (string) $status;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::CONTENT_BLOCKS_STATUS;
    }
}
