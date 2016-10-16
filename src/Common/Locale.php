<?php

namespace Common;

use InvalidArgumentException;
use Serializable;

abstract class Locale implements Serializable
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @param string $locale
     */
    protected function __construct($locale)
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
        return new static($locale);
    }

    /**
     * @return array
     */
    abstract protected function getPossibleLanguages();

    /**
     * @param string $locale
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    protected function setLocale($locale)
    {
        if (!array_key_exists($locale, $this->getPossibleLanguages())) {
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

    /**
     * @return string
     */
    public function serialize()
    {

        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return Locale
     */
    public function unserialize($locale)
    {
        $this->locale = $locale;
    }

    public function equals(Locale $locale)
    {
        return $this->locale === $locale->locale;
    }
}
