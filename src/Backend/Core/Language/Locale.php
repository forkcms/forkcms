<?php

namespace App\Backend\Core\Language;

use App\Common\Locale as CommonLocale;

final class Locale extends CommonLocale
{
    public static function workingLocale(): self
    {
        return new self(Language::getWorkingLanguage());
    }

    protected function getPossibleLanguages(): array
    {
        return Language::getWorkingLanguages();
    }
}
