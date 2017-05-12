<?php

namespace Frontend\Core\Language;

use Common\Locale as CommonLocale;

final class Locale extends CommonLocale
{
    public static function frontendLanguage(): self
    {
        return new self(FRONTEND_LANGUAGE);
    }

    protected function getPossibleLanguages(): array
    {
        return array_flip(Language::getActiveLanguages());
    }
}
