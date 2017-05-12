<?php

namespace Common\Doctrine\ValueObject;

final class SEOFollow
{
    private const NONE = 'none';
    private const FOLLOW = 'follow';
    private const NOFOLLOW = 'nofollow';
    public const POSSIBLE_VALUES = [
        self::NONE,
        self::FOLLOW,
        self::NOFOLLOW,
    ];

    /** @var string */
    private $SEOFollow;

    public function __construct(string $SEOFollow)
    {
        if (!in_array($SEOFollow, self::POSSIBLE_VALUES, true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->SEOFollow = $SEOFollow;
    }

    public static function fromString(string $SEOFollow): SEOFollow
    {
        return new self($SEOFollow);
    }

    public function __toString(): string
    {
        return (string) $this->SEOFollow;
    }

    public function equals(SEOFollow $SEOFollow): bool
    {
        if (!($SEOFollow instanceof $this)) {
            return false;
        }

        return $SEOFollow->SEOFollow === $this->SEOFollow;
    }

    public static function none(): SEOFollow
    {
        return new self(self::NONE);
    }

    public function isNone(): bool
    {
        return $this->equals(self::none());
    }

    public static function follow(): SEOFollow
    {
        return new self(self::FOLLOW);
    }

    public function isFollow(): bool
    {
        return $this->equals(self::follow());
    }

    public static function nofollow(): SEOFollow
    {
        return new self(self::NOFOLLOW);
    }

    public function isNofollow(): bool
    {
        return $this->equals(self::nofollow());
    }
}
