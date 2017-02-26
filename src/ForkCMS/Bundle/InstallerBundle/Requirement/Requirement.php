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

    /**
     * @param string $name
     * @param RequirementStatus $status
     * @param string $message
     */
    private function __construct(string $name, RequirementStatus $status, string $message)
    {
        $this->name = $name;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @param string $name
     * @param bool $requirementIsMet
     * @param string $requirementIsMetMessage
     * @param string $requirementNotMetMessage
     * @param RequirementStatus $requirementNotMetStatus
     *
     * @return self
     */
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return RequirementStatus
     */
    public function getStatus(): RequirementStatus
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
