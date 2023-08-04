<?php

namespace ForkCMS\Bundle\InstallerBundle\Language;

final class Locale extends \Common\Locale
{
    public const AVAILABLE_LOCALE = [
        'en' => 'English',
        'zh' => 'Chinese',
        'nl' => 'Dutch',
        'fr' => 'French',
        'de' => 'German',
        'el' => 'Greek',
        'hu' => 'Hungarian',
        'it' => 'Italian',
        'lt' => 'Lithuanian',
        'ru' => 'Russian',
        'es' => 'Spanish',
        'sv' => 'Swedish',
        'uk' => 'Ukrainian',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
    ];

    protected function getPossibleLanguages(): array
    {
        return self::AVAILABLE_LOCALE;
    }
}
