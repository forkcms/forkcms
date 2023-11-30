<?php

namespace ForkCMS\Modules\Installer\Domain\Installer;

enum InstallerStep: int
{
    case REQUIREMENTS = 1;
    case LOCALES = 2;
    case MODULES = 3;
    case DATABASE = 4;
    case AUTHENTICATION = 5;
    case INSTALL = 6;
    public function next(): self
    {
        return self::from($this->value + 1);
    }

    public function previous(): self
    {
        return self::from($this->value - 1);
    }

    public function hasPrevious(): bool
    {
        return self::tryFrom($this->value - 1) !== null;
    }

    public function route(): string
    {
        return 'install_step' . $this->value;
    }

    public function template(): string
    {
        return 'step' . $this->value . '.html.twig';
    }
}
