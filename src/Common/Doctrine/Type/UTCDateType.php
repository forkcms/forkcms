<?php

namespace Common\Doctrine\Type;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateType;

class UTCDateType extends DateType
{
    /** @var DateTimeZone */
    private static $utc;

    /** @var DateTimeZone */
    private static $defaultTimeZone;

    /**
     * @param DateTime $date
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($date, AbstractPlatform $platform)
    {
        if ($date instanceof DateTime) {
            $date->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($date, $platform);
    }

    /**
     * @param string $dateString
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     *
     * @return DateTime|null
     */
    public function convertToPHPValue($dateString, AbstractPlatform $platform)
    {
        if (null === $dateString || $dateString instanceof DateTime) {
            return $dateString;
        }

        $date = DateTime::createFromFormat($platform->getDateFormatString(), $dateString, self::getUtc());

        if (!$date) {
            throw ConversionException::conversionFailedFormat(
                $dateString,
                $this->getName(),
                $platform->getDateFormatString()
            );
        }

        // set time zone
        $date->setTimezone(self::getDefaultTimeZone());

        return $date;
    }

    /**
     * @return DateTimeZone
     */
    private static function getUtc()
    {
        return self::$utc ? self::$utc : self::$utc = new DateTimeZone('UTC');
    }

    /**
     * @return DateTimeZone
     */
    private static function getDefaultTimeZone()
    {
        return self::$defaultTimeZone
            ? self::$defaultTimeZone : self::$defaultTimeZone = new DateTimeZone(date_default_timezone_get());
    }
}
