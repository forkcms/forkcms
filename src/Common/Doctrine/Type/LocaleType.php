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

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'VARCHAR(5)';
    }

    /**
     * @param string|null $locale
     * @param AbstractPlatform $platform
     *
     * @return Locale|null
     */
    public function convertToPHPValue($locale, AbstractPlatform $platform): ?Locale
    {
        if ($locale === null) {
            return null;
        }

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
    public function convertToDatabaseValue($locale, AbstractPlatform $platform): string
    {
        return (string) $locale;
    }

    public function getName(): string
    {
        return self::LOCALE;
    }
}
