<?php

namespace App\Domain\ModuleExtra;

use App\Domain\ModuleExtra\Exception\InvalidTypeException;

final class Type
{
    private const BLOCK = 'block';
    private const HOMEPAGE = 'homepage';
    private const WIDGET = 'widget';
    public const POSSIBLE_TYPES = [
        self::BLOCK,
        self::HOMEPAGE,
        self::WIDGET,
    ];

    /** @var string */
    private $type;

    /**
     * @param string $type
     *
     * @throws InvalidTypeException
     */
    public function __construct(string $type)
    {
        if (!in_array($type, self::POSSIBLE_TYPES, true)) {
            throw InvalidTypeException::for($type);
        }

        $this->type = $type;
    }

    public static function block(): self
    {
        return new self(self::BLOCK);
    }

    public static function homepage(): self
    {
        return new self(self::HOMEPAGE);
    }

    public static function widget(): self
    {
        return new self(self::WIDGET);
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
