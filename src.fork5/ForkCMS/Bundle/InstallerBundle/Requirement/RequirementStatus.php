<?php

namespace ForkCMS\Bundle\InstallerBundle\Requirement;

use InvalidArgumentException;

final class RequirementStatus
{
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'danger';
    const POSSIBLE_VALUES = [
        self::SUCCESS,
        self::WARNING,
        self::ERROR,
    ];

    /** @var string */
    private $requirementStatus;

    public function __construct(string $requirementStatus)
    {
        if (!in_array($requirementStatus, self::POSSIBLE_VALUES, true)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->requirementStatus = $requirementStatus;
    }

    public function getValue(): string
    {
        return $this->requirementStatus;
    }

    public function __toString(): string
    {
        return (string) $this->requirementStatus;
    }

    public function equals(self $requirementStatus): bool
    {
        if (!($requirementStatus instanceof $this)) {
            return false;
        }

        return $requirementStatus->requirementStatus === $this->requirementStatus;
    }

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public function isSuccess(): bool
    {
        return $this->equals(self::success());
    }

    public static function warning(): self
    {
        return new self(self::WARNING);
    }

    public function isWarning(): bool
    {
        return $this->equals(self::warning());
    }

    public static function error(): self
    {
        return new self(self::ERROR);
    }

    public function isError(): bool
    {
        return $this->equals(self::error());
    }
}
