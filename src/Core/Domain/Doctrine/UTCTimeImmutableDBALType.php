<?php

namespace ForkCMS\Core\Domain\Doctrine;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\TimeImmutableType;

class UTCTimeImmutableDBALType extends TimeImmutableType
{
    use UTCDBALTrait;

    /**
     * @param DateTimeImmutable|null $time
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($time, AbstractPlatform $platform): ?string
    {
        if ($time instanceof DateTimeImmutable) {
            $time = $time->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($time, $platform);
    }

    /**
     * @param string|null|DateTimeImmutable $timeString
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     *
     * @return DateTimeImmutable|null
     */
    public function convertToPHPValue($timeString, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if (null === $timeString || $timeString instanceof DateTimeImmutable) {
            return $timeString;
        }

        $time = DateTimeImmutable::createFromFormat($platform->getTimeFormatString(), $timeString, self::getUtc());

        if (!$time) {
            throw ConversionException::conversionFailedFormat(
                $timeString,
                $this->getName(),
                $platform->getTimeFormatString()
            );
        }

        // set time zone
        return $time->setTimezone(self::getDefaultTimeZone());
    }
}
