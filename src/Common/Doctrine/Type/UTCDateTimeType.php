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
    public function convertToDatabaseValue($dateTime, AbstractPlatform $platform)
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
    public function convertToPHPValue($dateTimeString, AbstractPlatform $platform)
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

    /**
     * @return DateTimeZone
     */
    private static function getUtc(): DateTimeZone
    {
        return self::$utc ?: self::$utc = new DateTimeZone('UTC');
    }

    /**
     * @return DateTimeZone
     */
    private static function getDefaultTimeZone(): DateTimeZone
    {
        return self::$defaultTimeZone ?: self::$defaultTimeZone = new DateTimeZone(date_default_timezone_get());
    }
}
