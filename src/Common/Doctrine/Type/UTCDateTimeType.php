<?php

namespace Common\Doctrine\Type;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UTCDateTimeType extends DateTimeType
{
    /** @var DateTimeZone */
    private static $utc;

    /** @var DateTimeZone */
    private static $defaultTimeZone;

    /**
     * @param DateTime $dateTime
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($dateTime, AbstractPlatform $platform): ?string
    {
        if ($dateTime instanceof DateTime) {
            $dateTime->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($dateTime, $platform);
    }

    /**
     * @param string $dateTimeString
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     *
     * @return DateTime|null
     */
    public function convertToPHPValue($dateTimeString, AbstractPlatform $platform): ?DateTime
    {
        if (null === $dateTimeString || $dateTimeString instanceof DateTime) {
            return $dateTimeString;
        }

        $dateTime = DateTime::createFromFormat($platform->getDateTimeFormatString(), $dateTimeString, self::getUtc());

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat(
                $dateTimeString,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        // set time zone
        $dateTime->setTimezone(self::getDefaultTimeZone());

        return $dateTime;
    }

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
