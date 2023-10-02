<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use ForkCMS\Core\Domain\Identifier\NamedIdentifier;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;
use Symfony\Component\DependencyInjection\Container;

final class ModuleName implements Stringable, JsonSerializable
{
    use NamedIdentifier;

    public const ROLE_PREFIX = 'ROLE_MODULE__';

    public static function fromFQCN(string $fullyQualifiedClassName): self
    {
        $matches = [];
        if (
            !preg_match(
                '/^ForkCMS\\\Modules\\\([A-Z]\w*)/',
                $fullyQualifiedClassName,
                $matches
            )
        ) {
            if (preg_match('/^ForkCMS\\\Core\\\([A-Z]\w*)/', $fullyQualifiedClassName)) {
                return self::core();
            }

            throw new InvalidArgumentException('Can ony be created from a module classes: ' . $fullyQualifiedClassName);
        }

        return self::fromString($matches[1]);
    }

    public static function fromRole(string $role): self
    {
        return self::tryFromRole($role)
            ?? throw new InvalidArgumentException('Role should start with: ' . self::ROLE_PREFIX);
    }

    public static function tryFromRole(string $role): ?self
    {
        if (!str_starts_with($role, self::ROLE_PREFIX)) {
            return null;
        }

        return self::fromString(Container::camelize(strtolower(substr($role, strlen(self::ROLE_PREFIX)))));
    }

    public function asRole(): string
    {
        return self::ROLE_PREFIX . strtoupper(Container::underscore($this->getName()));
    }

    public static function core(): self
    {
        return self::fromString('Core');
    }

    public static function installer(): self
    {
        return self::fromString('Installer');
    }
}
