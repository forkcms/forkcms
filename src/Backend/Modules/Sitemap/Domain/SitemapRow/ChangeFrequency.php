<?php

namespace Backend\Modules\Sitemap\Domain\SitemapRow;

final class ChangeFrequency
{
    protected const ALWAYS = 'always';
    protected const HOURLY = 'hourly';
    protected const DAILY = 'daily';
    protected const WEEKLY = 'weekly';
    protected const MONTHLY = 'monthly';
    protected const YEARLY = 'yearly';
    protected const NEVER = 'never';

    public const POSSIBLE_VALUES = [
        self::ALWAYS,
        self::HOURLY,
        self::DAILY,
        self::WEEKLY,
        self::MONTHLY,
        self::YEARLY,
        self::NEVER,
    ];

    /** @var string */
    private $value;

    private function __construct($changeFrequency)
    {
        if (!in_array($changeFrequency, self::POSSIBLE_VALUES)) {
            throw new \Exception('The given changeFrequency is not allowed.');
        }

        $this->value = $changeFrequency;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ChangeFrequency $changeFrequency): bool
    {
        if (!($changeFrequency instanceof $this)) {
            return false;
        }

        return $changeFrequency == $this;
    }

    public static function always(): self
    {
        return new self(self::ALWAYS);
    }

    public function isAlways(): bool
    {
        return $this->equals(self::always());
    }

    public static function hourly(): self
    {
        return new self(self::HOURLY);
    }

    public function isHourly(): bool
    {
        return $this->equals(self::hourly());
    }

    public static function daily(): self
    {
        return new self(self::DAILY);
    }

    public function isDaily(): bool
    {
        return $this->equals(self::daily());
    }

    public static function weekly(): self
    {
        return new self(self::WEEKLY);
    }

    public function isWeekly(): bool
    {
        return $this->equals(self::weekly());
    }

    public static function monthly(): self
    {
        return new self(self::MONTHLY);
    }

    public function isMonthly(): bool
    {
        return $this->equals(self::monthly());
    }

    public static function yearly(): self
    {
        return new self(self::YEARLY);
    }

    public function isYearly(): bool
    {
        return $this->equals(self::yearly());
    }

    public static function never(): self
    {
        return new self(self::NEVER);
    }

    public function isNever(): bool
    {
        return $this->equals(self::never());
    }
}
