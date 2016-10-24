<?php

namespace Common\Doctrine\ValueObject;

final class SEOIndex
{
    const NONE = 'none';
    const INDEX = 'index';
    const NOINDEX = 'noindex';

    /**
     * @var string
     */
    private $SEOIndex;

    /**
     * @param string $SEOIndex
     */
    public function __construct($SEOIndex)
    {
        if (!in_array($SEOIndex, self::getPossibleValues())) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->SEOIndex = $SEOIndex;
    }

    /**
     * @param string $SEOIndex
     *
     * @return SEOIndex
     */
    public static function fromString($SEOIndex)
    {
        return new self($SEOIndex);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->SEOIndex;
    }

    /**
     * @param SEOIndex $SEOIndex
     *
     * @return bool
     */
    public function equals(SEOIndex $SEOIndex)
    {
        if (!($SEOIndex instanceof $this)) {
            return false;
        }

        return $SEOIndex == $this;
    }

    /**
     * @return array
     */
    public static function getPossibleValues()
    {
        return [
            self::NONE,
            self::INDEX,
            self::NOINDEX,
        ];
    }

    /**
     * @return SEOIndex
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
     * @return SEOIndex
     */
    public static function index()
    {
        return new self(self::INDEX);
    }

    /**
     * @return bool
     */
    public function isIndex()
    {
        return $this->equals(self::index());
    }

    /**
     * @return SEOIndex
     */
    public static function noindex()
    {
        return new self(self::NOINDEX);
    }

    /**
     * @return bool
     */
    public function isNoindex()
    {
        return $this->equals(self::noindex());
    }
}
