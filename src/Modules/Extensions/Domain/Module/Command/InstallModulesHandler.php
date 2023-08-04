<?php

namespace ForkCMS\Modules\Extensions\Domain\Module\Command;

use ForkCMS\Core\Domain\Kernel\Event\ClearCacheEvent;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\Module\Event\ModuleInstalledEvent;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use Psr\EventDispatcher\EventDispatcherInterface;

final class InstallModulesHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ModuleInstallerLocator $moduleInstallerLocator,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(InstallModules $installModules): void
    {
        $moduleInstallers = $this->moduleInstallerLocator->getSortedUninstalledInstallersForModuleNames(
            ...$installModules->getModuleNames()
        );

        foreach ($moduleInstallers as $moduleInstaller) {
            $moduleInstaller->preInstall();
        }

        foreach ($moduleInstallers as $moduleInstaller) {
            $moduleInstaller->registerModule();
            $moduleInstaller->install();
        }

        $this->eventDispatcher->dispatch(new ModuleInstalledEvent(...$installModules->getModuleNames()));
        $this->eventDispatcher->dispatch(new ClearCacheEvent());
    }
}
