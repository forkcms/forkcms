<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale;

use Locale as IntlLocale;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Locale: string implements TranslatableInterface
{
    case ENGLISH = 'en';
    case CHINESE = 'zh';
    case DUTCH = 'nl';
    case FRENCH = 'fr';
    case GERMAN = 'de';
    case GREEK = 'el';
    case HUNGARIAN = 'hu';
    case ITALIAN = 'it';
    case LITHUANIAN = 'lt';
    case RUSSIAN = 'ru';
    case SPANISH = 'es';
    case SWEDISH = 'sv';
    case UKRAINIAN = 'uk';
    case POLISH = 'pl';
    case PORTUGUESE = 'pt';
    case TURKISH = 'tr';

    public function asTranslatable(): string
    {
        return 'lbl.' . mb_strtoupper($this->value);
    }

    public static function fallback(): self
    {
        return self::ENGLISH;
    }

    public static function current(?self $locale = null): self
    {
        static $current = null;
        if ($locale !== null) {
            $current = $locale;
        }
        if ($current === null) {
            throw new RuntimeException('Locale::current() was called before Locale::current($locale) was called');
        }

        return $current;
    }

    public static function i18n(): self
    {
        return self::from(substr(IntlLocale::getDefault(), 0, 2));
    }

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans($this->asTranslatable(), [], null, $locale);
    }
}
