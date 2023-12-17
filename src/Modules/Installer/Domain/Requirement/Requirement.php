<?php

namespace ForkCMS\Modules\Installer\Domain\Requirement;

final class Requirement
{
    private function __construct(
        public readonly string $name,
        public readonly RequirementStatus $status,
        public readonly string $message
    ) {
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
            $requirementIsMet ? RequirementStatus::SUCCESS : $requirementNotMetStatus,
            $requirementIsMet ? $requirementIsMetMessage : $requirementNotMetMessage
        );
    }
}
