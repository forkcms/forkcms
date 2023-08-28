<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale;

use Locale as IntlLocale;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Locale: string implements TranslatableInterface
{
    case English = 'en';
    case Chinese = 'zh';
    case Dutch = 'nl';
    case French = 'fr';
    case German = 'de';
    case Greek = 'el';
    case Hungarian = 'hu';
    case Italian = 'it';
    case Lithuanian = 'lt';
    case Russian = 'ru';
    case Spanish = 'es';
    case Swedish = 'sv';
    case Ukrainian = 'uk';
    case Polish = 'pl';
    case Portuguese = 'pt';
    case Turkish = 'tr';
    public function asTranslatable(): string
    {
        return 'lbl.' . mb_strtoupper($this->value);
    }

    public static function fallback(): self
    {
        return self::English;
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
