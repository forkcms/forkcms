<?php

namespace ForkCMS\Core\Domain\Settings;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use ForkCMS\Core\Domain\Doctrine\ForkDBALTypeName;
use JsonException;

use function is_resource;
use function stream_get_contents;

final class SettingsBagDBALType extends JsonType
{
    use ForkDBALTypeName;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string|null
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof SettingsBag) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        try {
            return $value->asJsonString();
        } catch (JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): SettingsBag|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        try {
            return SettingsBag::fromJsonString($value);
        } catch (JsonException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
