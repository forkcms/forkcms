<?php

namespace ForkCMS\Core\Domain\Doctrine;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType;

class UTCDateTimeImmutableDBALType extends DateTimeImmutableType
{
    use UTCDBALTrait;

    /**
     * @param DateTimeImmutable|null $dateTime
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($dateTime, AbstractPlatform $platform): ?string
    {
        if ($dateTime instanceof DateTimeImmutable) {
            $dateTime = $dateTime->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($dateTime, $platform);
    }

    /**
     * @param string|null|DateTimeImmutable $dateTimeString
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     *
     * @return DateTimeImmutable|null
     */
    public function convertToPHPValue($dateTimeString, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if (null === $dateTimeString || $dateTimeString instanceof DateTimeImmutable) {
            return $dateTimeString;
        }

        $dateTime = DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(),
            $dateTimeString,
            self::getUtc()
        );

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat(
                $dateTimeString,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        // set time zone
        return $dateTime->setTimezone(self::getDefaultTimeZone());
    }
}
