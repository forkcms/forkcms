<?php

namespace ForkCMS\Modules\Installer\Domain\Installer;

enum InstallerStep: int
{
    case requirements = 1;
    case locales = 2;
    case modules = 3;
    case database = 4;
    case authentication = 5;
    case install = 6;
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
