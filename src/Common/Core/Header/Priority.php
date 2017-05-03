<?php

namespace Common\Core\Header;

use InvalidArgumentException;

final class Priority
{
    const CORE = 0;
    const STANDARD = 1;
    const MODULE = 2;
    const WIDGET = 3;
    const DEBUG = 99;

    const POSSIBLE_VALUES = [
        self::CORE,
        self::STANDARD,
        self::MODULE,
        self::WIDGET,
        self::DEBUG,
    ];

    /** @var int */
    private $priority;

    /**
     * @param int $priority
     */
    public function __construct(int $priority)
    {
        if (!in_array($priority, self::POSSIBLE_VALUES, true)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->priority;
    }

    /**
     * @param self $priority
     *
     * @return bool
     */
    public function equals(self $priority): bool
    {
        return $priority->priority === $this->priority;
    }

    /**
     * @param Priority $priority
     *
     * @return int
     */
    public function compare(self $priority): int
    {
        return $this->priority <=> $priority->priority;
    }

    /**
     * @return self
     */
    public static function core(): self
    {
        return new self(self::CORE);
    }

    /**
     * @return bool
     */
    public function isCore(): bool
    {
        return $this->equals(self::core());
    }

    /**
     * @return self
     */
    public static function debug(): self
    {
        return new self(self::DEBUG);
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->equals(self::debug());
    }

    /**
     * @return self
     */
    public static function standard(): self
    {
        return new self(self::STANDARD);
    }

    /**
     * @return bool
     */
    public function isStandard(): bool
    {
        return $this->equals(self::standard());
    }

    /**
     * @return self
     */
    public static function module(): self
    {
        return new self(self::MODULE);
    }

    /**
     * @return bool
     */
    public function isModule(): bool
    {
        return $this->equals(self::module());
    }

    /**
     * @return self
     */
    public static function widget(): self
    {
        return new self(self::WIDGET);
    }

    /**
     * @return bool
     */
    public function isWidget(): bool
    {
        return $this->equals(self::widget());
    }

    /**
     * @param string $module
     *
     * @return self
     */
    public static function forModule(string $module): self
    {
        if (ucfirst($module) === 'Core') {
            return self::core();
        }

        return self::module();
    }
}
