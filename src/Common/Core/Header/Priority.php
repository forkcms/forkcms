<?php

namespace Common\Core\Header;

use InvalidArgumentException;

final class Priority
{
    private const CORE = 0;
    private const STANDARD = 1;
    private const MODULE = 2;
    private const WIDGET = 3;
    private const DEBUG = 99;

    private const POSSIBLE_VALUES = [
        self::CORE,
        self::STANDARD,
        self::MODULE,
        self::WIDGET,
        self::DEBUG,
    ];

    /** @var int */
    private $priority;

    public function __construct(int $priority)
    {
        if (!in_array($priority, self::POSSIBLE_VALUES, true)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->priority = $priority;
    }

    public function getValue(): int
    {
        return $this->priority;
    }

    public function __toString(): string
    {
        return (string) $this->priority;
    }

    public function equals(self $priority): bool
    {
        return $priority->priority === $this->priority;
    }

    public function compare(self $priority): int
    {
        return $this->priority <=> $priority->priority;
    }

    public static function core(): self
    {
        return new self(self::CORE);
    }

    public function isCore(): bool
    {
        return $this->equals(self::core());
    }

    public static function debug(): self
    {
        return new self(self::DEBUG);
    }

    public function isDebug(): bool
    {
        return $this->equals(self::debug());
    }

    public static function standard(): self
    {
        return new self(self::STANDARD);
    }

    public function isStandard(): bool
    {
        return $this->equals(self::standard());
    }

    public static function module(): self
    {
        return new self(self::MODULE);
    }

    public function isModule(): bool
    {
        return $this->equals(self::module());
    }

    public static function widget(): self
    {
        return new self(self::WIDGET);
    }

    public function isWidget(): bool
    {
        return $this->equals(self::widget());
    }

    public static function forModule(string $module): self
    {
        if (ucfirst($module) === 'Core') {
            return self::core();
        }

        return self::module();
    }
}
