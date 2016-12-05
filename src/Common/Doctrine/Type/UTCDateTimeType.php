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

        return $dateTime;
    }

    /**
     * @return DateTimeZone
     */
    private static function getUtc()
    {
        return self::$utc ? self::$utc : self::$utc = new DateTimeZone('UTC');
    }
}
