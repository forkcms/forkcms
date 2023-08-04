<?php

namespace Backend\Modules\Pages\Domain\Page;

use InvalidArgumentException;

final class Type
{
    private const ROOT = 'root';
    private const PAGE = 'page';
    private const FOOTER = 'footer';
    private const META = 'meta';
    public const POSSIBLE_VALUES = [
        self::ROOT,
        self::PAGE,
        self::FOOTER,
        self::META,
    ];

    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        if (!in_array($type, self::POSSIBLE_VALUES, true)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->type = $type;
    }

    public function getValue(): string
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return (string) $this->type;
    }

    public function equals(self $type): bool
    {
        if (!($type instanceof $this)) {
            return false;
        }

        return $type->type === $this->type;
    }

    public static function root(): self
    {
        return new self(self::ROOT);
    }

    public function isRoot(): bool
    {
        return $this->equals(self::root());
    }

    public static function page(): self
    {
        return new self(self::PAGE);
    }

    public function isPage(): bool
    {
        return $this->equals(self::page());
    }

    public static function footer(): self
    {
        return new self(self::FOOTER);
    }

    public function isFooter(): bool
    {
        return $this->equals(self::footer());
    }

    public static function meta(): self
    {
        return new self(self::META);
    }

    public function isMeta(): bool
    {
        return $this->equals(self::meta());
    }
}
