<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup\Permission;

use Stringable;

final class Permission implements Stringable
{
    public function __construct(
        private string $value,
        private string $module,
        private string $name,
        private string $description
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
