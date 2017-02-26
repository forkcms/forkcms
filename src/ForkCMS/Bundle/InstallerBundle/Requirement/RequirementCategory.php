<?php

namespace ForkCMS\Bundle\InstallerBundle\Requirement;

final class RequirementCategory
{
    /** @var string */
    private $name;

    /** @var Requirement[] */
    private $requirements;

    /**
     * @param string $name
     * @param Requirement[] $requirements
     */
    public function __construct($name, Requirement ...$requirements)
    {
        $this->name = $name;
        $this->requirements = $requirements;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Requirement[]
     */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    /**
     * @return Requirement[]
     */
    public function getWarnings(): array
    {
        return array_filter(
            $this->requirements,
            function (Requirement $requirement) {
                return $requirement->getStatus()->isWarning();
            }
        );
    }

    /**
     * @return Requirement[]
     */
    public function getErrors(): array
    {
        return array_filter(
            $this->requirements,
            function (Requirement $requirement) {
                return $requirement->getStatus()->isError();
            }
        );
    }

    /**
     * @return bool
     */
    public function hasWarnings(): bool
    {
        return count($this->getWarnings()) > 0;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->getErrors()) > 0;
    }
}
