<?php

namespace Frontend\Core\Language;

use Common\Locale as CommonLocale;

final class Locale extends CommonLocale
{
    public static function frontendLanguage(): ?self
    {
        if (\defined('FRONTEND_LANGUAGE')) {
            return new self(FRONTEND_LANGUAGE);
        }

        return null;
    }

    protected function getPossibleLanguages(): array
    {
        return array_flip(Language::getActiveLanguages());
    }
}
