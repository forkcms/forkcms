<?php

namespace ForkCMS\Modules\Extensions\Domain\Module\Command;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;

final class InstallModules
{
    /** @var ModuleName[] */
    private array $moduleNames;

    public function __construct(ModuleName ...$moduleNames)
    {
        $this->moduleNames = $moduleNames;
    }

    /** @return ModuleName[] */
    public function getModuleNames(): array
    {
        return $this->moduleNames;
    }
}
