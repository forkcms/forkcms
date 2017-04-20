<?php

namespace Backend\Modules\ContentBlocks\ValueObject;

use InvalidArgumentException;

final class ContentBlockStatus
{
    const STATUS_ARCHIVED = 'archived';
    const STATUS_ACTIVE = 'active';

    /** @var string */
    private $status;

    /**
     * @param string $status
     */
    private function __construct(string $status)
    {
        $this->setStatus($status);
    }

    /**
     * @return array
     */
    public static function getPossibleStatuses(): array
    {
        return [
            self::STATUS_ARCHIVED,
            self::STATUS_ACTIVE,
        ];
    }

    /**
     * @param $status
     *
     * @return self
     */
    public static function fromString($status): self
    {
        return new self($status);
    }

    /**
     * @param string $status
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    private function setStatus(string $status): self
    {
        if (!in_array($status, self::getPossibleStatuses(), true)) {
            throw new InvalidArgumentException('Invalid status');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * @return self
     */
    public static function active(): self
    {
        return new self(self::STATUS_ACTIVE);
    }

    /**
     * @return self
     */
    public static function archived(): self
    {
        return new self(self::STATUS_ARCHIVED);
    }
}
