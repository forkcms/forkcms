<?php

namespace Backend\Doctrine\Type;

use Backend\Core\Language\LanguageName;
use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class LanguageType extends TextType
{
    const LANGUAGE = 'language';

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
     * @param string $language
     * @param AbstractPlatform $platform
     *
     * @return LanguageName
     */
    public function convertToPHPValue($language, AbstractPlatform $platform)
    {
        return LanguageName::fromString($language);
    }

    /**
     * @param LanguageName $languageName
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($languageName, AbstractPlatform $platform)
    {
        return (string) $languageName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::LANGUAGE;
    }
}
