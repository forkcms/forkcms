<?php

namespace Backend\Core\Language;

use InvalidArgumentException;

final class LanguageName
{
    /**
     * @var string
     */
    private $language;

    /**
     * @param string $language
     */
    private function __construct($language)
    {
        $this->setLanguage($language);
    }

    /**
     * @param $language
     *
     * @return self
     */
    public static function fromString($language)
    {
        return new self($language);
    }

    /**
     * @return self
     */
    public static function workingLanguage()
    {
        return new self(Language::getWorkingLanguage());
    }

    /**
     * @param string $language
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    private function setLanguage($language)
    {
        if (!in_array($language, Language::getWorkingLanguages())) {
            throw new InvalidArgumentException('Invalid language');
        }

        $this->language = $language;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
