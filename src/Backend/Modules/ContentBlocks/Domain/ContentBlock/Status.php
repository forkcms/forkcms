<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock;

use InvalidArgumentException;

final class Status
{
    private const STATUS_ARCHIVED = 'archived';
    private const STATUS_ACTIVE = 'active';

    /** @var string */
    private $status;

    private function __construct(string $status)
    {
        $this->setStatus($status);
    }

    public static function getPossibleStatuses(): array
    {
        return [
            self::STATUS_ARCHIVED,
            self::STATUS_ACTIVE,
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

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public static function active(): self
    {
        return new self(self::STATUS_ACTIVE);
    }

    public static function archived(): self
    {
        return new self(self::STATUS_ARCHIVED);
    }
}
