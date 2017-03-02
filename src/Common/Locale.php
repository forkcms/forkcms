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
    protected function __construct(string $locale)
    {
        $this->setLocale($locale);
    }

    /**
     * @param string $locale
     *
     * @return self
     */
    public static function fromString(string $locale): self
    {
        return new static($locale);
    }

    /**
     * @return array
     */
    abstract protected function getPossibleLanguages(): array;

    /**
     * @param string $locale
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    protected function setLocale(string $locale): self
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
    public function getLocale(): string
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

    /**
     * @param Locale $locale
     *
     * @return bool
     */
    public function equals(Locale $locale): bool
    {
        return $this->locale === $locale->locale;
    }
}
