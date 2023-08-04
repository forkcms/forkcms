<?php

namespace ForkCMS\Modules\Installer\Domain\Requirement;

final class RequirementCategory
{
    /** @var Requirement[] */
    public readonly array $requirements;

    public function __construct(public readonly string $name, Requirement ...$requirements)
    {
        $this->requirements = $requirements;
    }

    /** @return Requirement[] */
    public function getWarnings(): array
    {
        return array_filter(
            $this->requirements,
            static fn (Requirement $requirement) => $requirement->status->isWarning()
        );
    }

    /** @return Requirement[] */
    public function getErrors(): array
    {
        return array_filter(
            $this->requirements,
            static fn (Requirement $requirement) => $requirement->status->isError()
        );
    }

    public function hasWarnings(): bool
    {
        return count($this->getWarnings()) > 0;
    }

    public function hasErrors(): bool
    {
        return count($this->getErrors()) > 0;
    }
}
