<?php

namespace ForkCMS\Bundle\InstallerBundle\Requirement;

final class Requirement
{
    /** @var string */
    private $name;

    /** @var RequirementStatus */
    private $status;

    /** @var string */
    private $message;

    private function __construct(string $name, RequirementStatus $status, string $message)
    {
        $this->name = $name;
        $this->status = $status;
        $this->message = $message;
    }

    public static function check(
        string $name,
        bool $requirementIsMet,
        string $requirementIsMetMessage,
        string $requirementNotMetMessage,
        RequirementStatus $requirementNotMetStatus
    ): self {
        return new self(
            $name,
            $requirementIsMet ? RequirementStatus::success() : $requirementNotMetStatus,
            $requirementIsMet ? $requirementIsMetMessage : $requirementNotMetMessage
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): RequirementStatus
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
