<?php

namespace Backend\Modules\Profiles\Domain\Profile;

use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class StatusDBALType extends StringType
{
    const PROFILES_STATUS = 'profiles_status';

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
        return self::PROFILES_STATUS;
    }
}
