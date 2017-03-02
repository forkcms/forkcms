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
    public function __construct(string $SEOIndex)
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
    public static function fromString(string $SEOIndex): SEOIndex
    {
        return new self($SEOIndex);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->SEOIndex;
    }

    /**
     * @param SEOIndex $SEOIndex
     *
     * @return bool
     */
    public function equals(SEOIndex $SEOIndex): bool
    {
        if (!($SEOIndex instanceof $this)) {
            return false;
        }

        return $SEOIndex == $this;
    }

    /**
     * @return array
     */
    public static function getPossibleValues(): array
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
    public static function none(): SEOIndex
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
     * @return SEOIndex
     */
    public static function index(): SEOIndex
    {
        return new self(self::INDEX);
    }

    /**
     * @return bool
     */
    public function isIndex(): bool
    {
        return $this->equals(self::index());
    }

    /**
     * @return SEOIndex
     */
    public static function noindex(): SEOIndex
    {
        return new self(self::NOINDEX);
    }

    /**
     * @return bool
     */
    public function isNoindex(): bool
    {
        return $this->equals(self::noindex());
    }
}
