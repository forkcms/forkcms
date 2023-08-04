<?php

namespace ForkCMS\Core\Domain\Identifier;

use Assert\Assertion;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

trait NamedIdentifier
{
    private string $name;

    /** @var array<string, static> */
    private static array $nameInstances = [];

    private function __construct(string $name)
    {
        Assertion::regex($name, '/^[A-Z][A-Za-z0-9]*$/', 'Invalid name: ' . $name);

        $this->name = $name;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public static function fromString(string $name): static
    {
        if (!array_key_exists($name, self::$nameInstances)) {
            self::$nameInstances[$name] = new self($name);
        }

        return self::$nameInstances[$name];
    }

    final public function __toString(): string
    {
        return $this->name;
    }

    final public function asLabel(): TranslationKey
    {
        return TranslationKey::label($this->getName());
    }

    final public function jsonSerialize(): string
    {
        return $this->name;
    }
}
