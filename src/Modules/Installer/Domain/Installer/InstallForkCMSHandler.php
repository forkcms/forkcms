<?php

namespace ForkCMS\Modules\Installer\Domain\Installer;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\Module\Command\InstallModules;
use ForkCMS\Modules\Installer\Domain\Configuration\ConfigurationParser;
use Symfony\Component\Messenger\MessageBusInterface;

final class InstallForkCMSHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ConfigurationParser $configurationParser,
        private readonly MessageBusInterface $commandBus
    ) {
    }

    public function __invoke(InstallForkCMS $installForkCMS): void
    {
        // extend execution limit
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $installerConfiguration = $installForkCMS->getInstallerConfiguration();
        if ($installerConfiguration->shouldSaveConfiguration()) {
            $this->configurationParser->toYamlFile($installForkCMS->getInstallerConfiguration());
        }

        $this->commandBus->dispatch(new InstallModules(...$installerConfiguration->getModules()));

        $this->configurationParser->toDotEnvFile($installerConfiguration);
    }
}
