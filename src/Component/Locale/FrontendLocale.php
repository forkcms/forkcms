<?php

namespace App\Component\Locale;

final class FrontendLocale extends Locale
{
    public static function frontendLanguage(): self
    {
        return new self(FRONTEND_LANGUAGE);
    }

    protected function getPossibleLanguages(): array
    {
        return array_flip(FrontendLanguage::getActiveLanguages());
    }
}
