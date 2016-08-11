<?php

namespace Backend\Doctrine\Type;

use Backend\Core\Language\Locale;
use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class LocaleType extends TextType
{
    const LOCALE = 'locale';

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'VARCHAR(10)';
    }

    /**
     * @param string $locale
     * @param AbstractPlatform $platform
     *
     * @return Locale
     */
    public function convertToPHPValue($locale, AbstractPlatform $platform)
    {
        return Locale::fromString($locale);
    }

    /**
     * @param Locale $locale
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($locale, AbstractPlatform $platform)
    {
        return (string) $locale;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::LOCALE;
    }
}
