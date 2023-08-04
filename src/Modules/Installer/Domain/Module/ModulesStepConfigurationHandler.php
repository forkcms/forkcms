<?php

namespace ForkCMS\Modules\Installer\Domain\Module;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use Symfony\Component\Messenger\MessageBusInterface;

final class ModulesStepConfigurationHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ModuleInstallerLocator $moduleInstallerLocator,
        private readonly MessageBusInterface $commandBus
    ) {
    }

    public function __invoke(ModulesStepConfiguration $modulesStepConfiguration): void
    {
        InstallerConfiguration::toCache(
            InstallerConfiguration::fromCache()->withModulesStep(
                $modulesStepConfiguration,
                $this->moduleInstallerLocator,
                $this->commandBus
            )
        );
    }
}
