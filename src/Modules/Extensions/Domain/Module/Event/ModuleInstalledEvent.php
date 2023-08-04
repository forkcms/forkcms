<?php

namespace ForkCMS\Modules\Extensions\Domain\Module\Event;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use Symfony\Contracts\EventDispatcher\Event;

final class ModuleInstalledEvent extends Event
{
    /** @var ModuleName[] */
    public readonly array $moduleNames;

    public function __construct(Modulename ...$moduleName)
    {
        $this->moduleNames = $moduleName;
    }
}
