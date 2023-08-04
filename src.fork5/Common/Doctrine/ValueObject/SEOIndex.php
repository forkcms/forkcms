<?php

namespace Common\Doctrine\ValueObject;

final class SEOIndex
{
    private const NONE = 'none';
    private const INDEX = 'index';
    private const NOINDEX = 'noindex';
    public const POSSIBLE_VALUES = [
        self::NONE,
        self::INDEX,
        self::NOINDEX,
    ];

    /**
     * @var string
     */
    private $SEOIndex;

    public function __construct(string $SEOIndex)
    {
        if (!in_array($SEOIndex, self::POSSIBLE_VALUES, true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->SEOIndex = $SEOIndex;
    }

    public static function fromString(string $SEOIndex): SEOIndex
    {
        return new self($SEOIndex);
    }

    public function __toString(): string
    {
        return (string) $this->SEOIndex;
    }

    public function equals(SEOIndex $SEOIndex): bool
    {
        if (!($SEOIndex instanceof $this)) {
            return false;
        }

        return $SEOIndex->SEOIndex === $this->SEOIndex;
    }

    public static function none(): SEOIndex
    {
        return new self(self::NONE);
    }

    public function isNone(): bool
    {
        return $this->equals(self::none());
    }

    public static function index(): SEOIndex
    {
        return new self(self::INDEX);
    }

    public function isIndex(): bool
    {
        return $this->equals(self::index());
    }

    public static function noindex(): SEOIndex
    {
        return new self(self::NOINDEX);
    }

    public function isNoindex(): bool
    {
        return $this->equals(self::noindex());
    }
}
