<?php

namespace App\Common\Doctrine\Type;

use App\Backend\Core\Language\Locale as BackendLocale;
use App\Common\Locale;
use App\Frontend\Core\Language\Locale as FrontendLocale;
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
     * @param string $locale
     * @param AbstractPlatform $platform
     *
     * @return Locale
     */
    public function convertToPHPValue($locale, AbstractPlatform $platform): Locale
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
    public function convertToDatabaseValue($locale, AbstractPlatform $platform): string
    {
        return (string) $locale;
    }

    public function getName(): string
    {
        return self::LOCALE;
    }
}
