<?php

namespace Backend\Modules\Sitemap\Domain\SitemapRow;

final class ChangeFrequency
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $changeFrequency
     */
    private function __construct($changeFrequency)
    {
        $this->value = $changeFrequency;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return ChangeFrequency
     */
    public static function always(): self
    {
        return new self('always');
    }

    /**
     * @return ChangeFrequency
     */
    public static function hourly(): self
    {
        return new self('hourly');
    }

    /**
     * @return ChangeFrequency
     */
    public static function daily(): self
    {
        return new self('daily');
    }

    /**
     * @return ChangeFrequency
     */
    public static function weekly(): self
    {
        return new self('weekly');
    }

    /**
     * @return ChangeFrequency
     */
    public static function monthly(): self
    {
        return new self('monthly');
    }

    /**
     * @return ChangeFrequency
     */
    public static function yearly(): self
    {
        return new self('yearly');
    }

    /**
     * @return ChangeFrequency
     */
    public static function never(): self
    {
        return new self('never');
    }
}
