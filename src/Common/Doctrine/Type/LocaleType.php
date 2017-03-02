<?php

namespace Common\Doctrine\Type;

use Backend\Core\Language\Locale as BackendLocale;
use Common\Locale;
use Frontend\Core\Language\Locale as FrontendLocale;
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
        return 'VARCHAR(5)';
    }

    /**
     * @param string $locale
     * @param AbstractPlatform $platform
     *
     * @return Locale
     */
    public function convertToPHPValue($locale, AbstractPlatform $platform)
    {
        if (APPLICATION === 'Frontend') {
            return FrontendLocale::fromString($locale);
        }

        return BackendLocale::fromString($locale);
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
