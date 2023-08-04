<?php

namespace ForkCMS\Core\Domain\Doctrine;

use DateTimeZone;

trait UTCDBALTrait
{
    private static ?DateTimeZone $utc = null;

    private static ?DateTimeZone $defaultTimeZone = null;

    private static function getUtc(): DateTimeZone
    {
        if (self::$utc === null) {
            self::$utc = new DateTimeZone('UTC');
        }

        return self::$utc;
    }

    private static function getDefaultTimeZone(): DateTimeZone
    {
        if (self::$defaultTimeZone === null) {
            self::$defaultTimeZone = new DateTimeZone(date_default_timezone_get());
        }

        return self::$defaultTimeZone;
    }
}
