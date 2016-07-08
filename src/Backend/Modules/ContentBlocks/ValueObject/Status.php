<?php

namespace Backend\Modules\ContentBlocks\ValueObject;

use InvalidArgumentException;

final class Status
{
    const STATUS_ARCHIVED = 'archived';
    const STATUS_ACTIVE = 'active';

    /**
     * @var string
     */
    private $status;

    /**
     * @param string $status
     */
    private function __construct($status)
    {
        $this->setStatus($status);
    }

    /**
     * @return array
     */
    public static function getPossibleStatuses()
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
    public static function fromString($status)
    {
        return new self($status);
    }

    /**
     * @param string $status
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    private function setStatus($status)
    {
        if (!in_array($status, self::getPossibleStatuses())) {
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
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isArchived()
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * @return self
     */
    public static function active()
    {
        return new self(self::STATUS_ACTIVE);
    }

    /**
     * @return self
     */
    public static function archived()
    {
        return new self(self::STATUS_ARCHIVED);
    }
}
