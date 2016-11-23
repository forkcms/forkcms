<?php

namespace Common\Doctrine\ValueObject;

use Serializable;

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
    public function __construct($SEOFollow)
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
    public static function fromString($SEOFollow)
    {
        return new self($SEOFollow);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->SEOFollow;
    }

    /**
     * @param SEOFollow $SEOFollow
     *
     * @return bool
     */
    public function equals(SEOFollow $SEOFollow)
    {
        if (!($SEOFollow instanceof $this)) {
            return false;
        }

        return $SEOFollow == $this;
    }

    /**
     * @return array
     */
    public static function getPossibleValues()
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
    public static function none()
    {
        return new self(self::NONE);
    }

    /**
     * @return bool
     */
    public function isNone()
    {
        return $this->equals(self::none());
    }

    /**
     * @return SEOFollow
     */
    public static function follow()
    {
        return new self(self::FOLLOW);
    }

    /**
     * @return bool
     */
    public function isFollow()
    {
        return $this->equals(self::follow());
    }

    /**
     * @return SEOFollow
     */
    public static function nofollow()
    {
        return new self(self::NOFOLLOW);
    }

    /**
     * @return bool
     */
    public function isNofollow()
    {
        return $this->equals(self::nofollow());
    }
}
