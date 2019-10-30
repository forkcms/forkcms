<?php

namespace Backend\Modules\Profiles\Domain\Profile;

use InvalidArgumentException;

final class Status
{
    private const STATUS_ACTIVE = 'active';
    private const STATUS_INACTIVE = 'inactive';
    private const STATUS_DELETED = 'deleted';
    private const STATUS_BLOCKED = 'blocked';
    private const STATUS_INVALID = 'invalid';

    /** @var string */
    private $status;

    private function __construct(string $status)
    {
        $this->setStatus($status);
    }

    public static function getPossibleStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_DELETED,
            self::STATUS_BLOCKED,
        ];
    }

    public static function fromString($status): self
    {
        return new self($status);
    }

    private function setStatus(string $status): self
    {
        if (!in_array($status, self::getPossibleStatuses(), true)) {
            throw new InvalidArgumentException('Invalid status');
        }

        $this->status = $status;

        return $this;
    }

    public function __toString(): string
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function isDeleted(): bool
    {
        return $this->status === self::STATUS_DELETED;
    }

    public function isInvalid(): bool
    {
        return $this->status === self::STATUS_INVALID;
    }

    public static function active(): self
    {
        return new self(self::STATUS_ACTIVE);
    }

    public static function inactive(): self
    {
        return new self(self::STATUS_INACTIVE);
    }

    public static function blocked(): self
    {
        return new self(self::STATUS_BLOCKED);
    }

    public static function deleted(): self
    {
        return new self(self::STATUS_DELETED);
    }

    public static function invalid(): self
    {
        return new self(self::STATUS_INVALID);
    }
}
