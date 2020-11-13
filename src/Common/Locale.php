<?php

namespace Common;

use InvalidArgumentException;
use JsonSerializable;
use Serializable;

abstract class Locale implements Serializable, JsonSerializable
{
    /**
     * @var string
     */
    protected $locale;

    final protected function __construct(string $locale)
    {
        $this->setLocale($locale);
    }

    /**
     * @param string $locale
     *
     * @return static
     */
    public static function fromString(string $locale): self
    {
        return new static($locale);
    }

    abstract protected function getPossibleLanguages(): array;

    /**
     * @param string $locale
     *
     * @return static
     */
    protected function setLocale(string $locale): self
    {
        if (!array_key_exists($locale, $this->getPossibleLanguages())) {
            throw new InvalidArgumentException('Invalid language');
        }

        $this->locale = $locale;

        return $this;
    }

    public function __toString(): string
    {
        return $this->locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function serialize(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function unserialize($locale): void
    {
        $this->locale = $locale;
    }

    public function equals(Locale $locale): bool
    {
        return $this->locale === $locale->locale;
    }

    public function jsonSerialize(): string
    {
        return $this->locale;
    }
}
