<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ModuleTwigExtension extends AbstractExtension
{
    public function __construct(private readonly ModuleSettings $moduleSettings)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_module_setting',
                fn (string|ModuleName $module, string $key, mixed $default = null) => $this->moduleSettings->get(
                    is_string($module) ? ModuleName::fromString($module) : $module,
                    $key,
                    $default
                )
            ),
        ];
    }
}
