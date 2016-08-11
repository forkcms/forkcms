<?php

namespace Backend\Core\Language;

use InvalidArgumentException;

final class Locale
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $locale
     */
    private function __construct($locale)
    {
        $this->setLocale($locale);
    }

    /**
     * @param $locale
     *
     * @return self
     */
    public static function fromString($locale)
    {
        return new self($locale);
    }

    /**
     * @return self
     */
    public static function workingLocale()
    {
        return new self(Language::getWorkingLanguage());
    }

    /**
     * @param string $locale
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    private function setLocale($locale)
    {
        if (!array_key_exists($locale, Language::getWorkingLanguages())) {
            throw new InvalidArgumentException('Invalid language');
        }

        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
