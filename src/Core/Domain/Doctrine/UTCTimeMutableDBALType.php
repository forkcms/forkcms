<?php

namespace ForkCMS\Core\Domain\Doctrine;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\TimeType;

class UTCTimeMutableDBALType extends TimeType
{
    use UTCDBALTrait;

    /**
     * @param DateTime|null $time
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($time, AbstractPlatform $platform): ?string
    {
        if ($time instanceof DateTime) {
            $time->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($time, $platform);
    }

    /**
     * @param string|null|DateTime $timeString
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     *
     * @return DateTime|null
     */
    public function convertToPHPValue($timeString, AbstractPlatform $platform): ?DateTime
    {
        if (null === $timeString || $timeString instanceof DateTime) {
            return $timeString;
        }

        $time = DateTime::createFromFormat($platform->getTimeFormatString(), $timeString, self::getUtc());

        if (!$time) {
            throw ConversionException::conversionFailedFormat(
                $timeString,
                $this->getName(),
                $platform->getTimeFormatString()
            );
        }

        // set time zone
        $time->setTimezone(self::getDefaultTimeZone());

        return $time;
    }
}
