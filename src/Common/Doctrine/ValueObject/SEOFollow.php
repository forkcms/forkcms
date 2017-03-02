<?php

namespace Common\Doctrine\ValueObject;

final class SEOFollow
{
    const NONE = 'none';
    const FOLLOW = 'follow';
    const NOFOLLOW = 'nofollow';

    /**
     * @var string
     */
    private $SEOFollow;

    /**
     * @param string $SEOFollow
     */
    public function __construct(string $SEOFollow)
    {
        if (!in_array($SEOFollow, self::getPossibleValues())) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->SEOFollow = $SEOFollow;
    }

    /**
     * @param string $SEOFollow
     *
     * @return SEOFollow
     */
    public static function fromString(string $SEOFollow): SEOFollow
    {
        return new self($SEOFollow);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->SEOFollow;
    }

    /**
     * @param SEOFollow $SEOFollow
     *
     * @return bool
     */
    public function equals(SEOFollow $SEOFollow): bool
    {
        if (!($SEOFollow instanceof $this)) {
            return false;
        }

        return $SEOFollow == $this;
    }

    /**
     * @return array
     */
    public static function getPossibleValues(): array
    {
        return [
            self::NONE,
            self::FOLLOW,
            self::NOFOLLOW,
        ];
    }

    /**
     * @return SEOFollow
     */
    public static function none(): SEOFollow
    {
        return new self(self::NONE);
    }

    /**
     * @return bool
     */
    public function isNone(): bool
    {
        return $this->equals(self::none());
    }

    /**
     * @return SEOFollow
     */
    public static function follow(): SEOFollow
    {
        return new self(self::FOLLOW);
    }

    /**
     * @return bool
     */
    public function isFollow(): bool
    {
        return $this->equals(self::follow());
    }

    /**
     * @return SEOFollow
     */
    public static function nofollow(): SEOFollow
    {
        return new self(self::NOFOLLOW);
    }

    /**
     * @return bool
     */
    public function isNofollow(): bool
    {
        return $this->equals(self::nofollow());
    }
}
