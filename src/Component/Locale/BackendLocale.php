<?php

namespace App\Component\Locale;

final class BackendLocale extends Locale
{
    public static function workingLocale(): self
    {
        return new self(BackendLanguage::getWorkingLanguage());
    }

    protected function getPossibleLanguages(): array
    {
        return BackendLanguage::getWorkingLanguages();
    }
}
