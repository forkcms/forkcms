<?php

namespace Backend\Core\Language;

use Common\Locale as CommonLocale;

final class Locale extends CommonLocale
{
    /**
     * @return self
     */
    public static function workingLocale(): self
    {
        return new self(Language::getWorkingLanguage());
    }

    /**
     * {@inheritdoc}
     */
    protected function getPossibleLanguages(): array
    {
        return Language::getWorkingLanguages();
    }
}
